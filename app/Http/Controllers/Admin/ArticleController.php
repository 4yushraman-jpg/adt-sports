<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $articles   = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $article    = null;
        return view('admin.articles.editor', compact('categories', 'article'));
    }

    public function store(Request $request)
    {
        $data = $this->validateArticle($request);
        $data['status'] = $this->resolveStatus($request);
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);

        $data['author_id'] = Auth::id();
        $data['read_time'] = Article::calculateReadTime($data['body'] ?? '');
        $data['tags']      = $this->parseTags($request->tags ?? '');
        $data['published_at'] = $this->resolvePublishDate($data, $request);

        $article = $this->createWithUniqueSlug($data); // category counts refreshed by ArticleObserver
        $this->syncCategories($article, $categoryIds);

        $msg = $data['status'] === 'published' ? '🚀 Article published successfully!' : '💾 Draft saved.';
        return redirect()->route('admin.articles.edit', $article)->with('success', $msg);
    }

    public function edit(Article $article)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.articles.editor', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorizeArticle($article);

        $data = $this->validateArticle($request);
        $data['status'] = $this->resolveStatus($request);
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);

        $data['read_time'] = Article::calculateReadTime($data['body'] ?? '');
        $data['tags']      = $this->parseTags($request->tags ?? '');
        $data['published_at'] = $this->resolvePublishDate($data, $request, $article);

        $article->update($data); // category counts refreshed by ArticleObserver
        $this->syncCategories($article, $categoryIds);

        $msg = $data['status'] === 'published' ? '🚀 Published!' : '💾 Changes saved.';
        return back()->with('success', $msg);
    }

    public function destroy(Article $article)
    {
        $this->authorizeArticle($article);
        $article->delete(); // soft delete -> Trash; counts refreshed by ArticleObserver
        return redirect()->route('admin.articles.index')->with('success', '🗑️ Moved to Trash.');
    }

    public function trash()
    {
        $articles = Article::onlyTrashed()
            ->with(['category', 'author'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('admin.articles.trash', compact('articles'));
    }

    public function restore(string $id)
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $this->authorizeArticle($article);
        $article->restore();

        return back()->with('success', '♻️ Article restored.');
    }

    public function forceDestroy(string $id)
    {
        $article = Article::onlyTrashed()->findOrFail($id);
        $this->authorizeArticle($article);
        $article->forceDelete();

        return back()->with('success', 'Article permanently deleted.');
    }

    /** Only an admin or the article's own author may mutate it. */
    private function authorizeArticle(Article $article): void
    {
        if (! Auth::user()->isAdmin() && $article->author_id !== Auth::id()) {
            abort(403, 'Permission denied.');
        }
    }

    private function validateArticle(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:500',
            'excerpt'     => 'nullable|string|max:1000',
            'body'        => 'nullable|string',
            'cover_image' => 'nullable|string|max:500',
            'cover_emoji' => 'nullable|string|max:20',
            'cover_bg'    => 'nullable|string|max:300',
            'category_id' => 'nullable|exists:categories,id',
            'featured'     => 'nullable',
            'breaking'     => 'nullable',
            'meta_title'   => 'nullable|string|max:255',
            'meta_desc'    => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            'categories'   => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);
    }

    /** Store the article's additional categories (the primary stays on category_id). */
    private function syncCategories(Article $article, array $categoryIds): void
    {
        $additional = collect($categoryIds)
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === (int) $article->category_id) // don't duplicate the primary
            ->unique()
            ->values()
            ->all();

        $article->categories()->sync($additional);
    }

    /**
     * Status is driven by which button was pressed (Save Draft / Publish),
     * not a separate dropdown. Falls back to a posted 'status' field, then draft.
     */
    private function resolveStatus(Request $request): string
    {
        $status = $request->input('status_override', $request->input('status', 'draft'));

        return in_array($status, ['draft', 'published'], true) ? $status : 'draft';
    }

    /**
     * Decide the publish timestamp:
     *  - draft            -> null (a draft has no publish date)
     *  - published + date -> that date (future date = scheduled; it goes live
     *                         automatically once now() passes it)
     *  - published, none  -> now()
     */
    private function resolvePublishDate(array $data, Request $request, ?Article $existing = null): ?Carbon
    {
        if (($data['status'] ?? null) !== 'published') {
            return null;
        }

        if ($request->filled('published_at')) {
            return Carbon::parse($request->input('published_at'));
        }

        // Keep an already-published post's original date; otherwise publish now.
        return $existing?->published_at ?? now();
    }

    private function parseTags(string $raw): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $raw))
        ));
    }

    /**
     * Create an article with a unique slug, tolerant of the check-then-insert
     * race: if a concurrent publish grabbed the same slug, the DB unique
     * constraint fires and we retry with a random suffix instead of 500ing.
     */
    private function createWithUniqueSlug(array $data): Article
    {
        $base = Str::slug($data['title']) ?: 'article';
        $data['slug'] = Article::generateSlug($data['title']);

        for ($attempt = 0; ; $attempt++) {
            try {
                return Article::create($data);
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt >= 5) {
                    throw $e;
                }
                $data['slug'] = $base . '-' . Str::lower(Str::random(6));
            }
        }
    }
}
