<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Button that submitted determines status
        if ($request->has('status_override')) {
            $data['status'] = $request->status_override;
        }

        $data['author_id'] = Auth::id();
        $data['slug']      = Article::generateSlug($data['title']);
        $data['read_time'] = Article::calculateReadTime($data['body'] ?? '');
        $data['tags']      = $this->parseTags($request->tags ?? '');

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $article = Article::create($data);
        $article->category?->refreshCount();

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
        if (!Auth::user()->isAdmin() && $article->author_id !== Auth::id()) {
            abort(403, 'Permission denied.');
        }

        $data = $this->validateArticle($request);

        if ($request->has('status_override')) {
            $data['status'] = $request->status_override;
        }

        $data['read_time'] = Article::calculateReadTime($data['body'] ?? '');
        $data['tags']      = $this->parseTags($request->tags ?? '');

        if ($data['status'] === 'published' && $article->status !== 'published') {
            $data['published_at'] = now();
        }

        $oldCatId = $article->category_id;
        $article->update($data);

        if ($oldCatId) Category::find($oldCatId)?->refreshCount();
        $article->category?->refreshCount();

        $msg = $data['status'] === 'published' ? '🚀 Published!' : '💾 Changes saved.';
        return back()->with('success', $msg);
    }

    public function destroy(Article $article)
    {
        if (!Auth::user()->isAdmin() && $article->author_id !== Auth::id()) {
            abort(403);
        }
        $cat = $article->category;
        $article->delete();
        $cat?->refreshCount();
        return redirect()->route('admin.articles.index')->with('success', 'Article deleted.');
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
            'status'      => 'required|in:draft,published',
            'featured'    => 'nullable',
            'breaking'    => 'nullable',
            'meta_title'  => 'nullable|string|max:255',
            'meta_desc'   => 'nullable|string|max:500',
        ]);
    }

    private function parseTags(string $raw): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $raw))
        ));
    }
}
