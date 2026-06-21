<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;
use Mews\Purifier\Facades\Purifier;
use App\Observers\ArticleObserver;

#[ObservedBy(ArticleObserver::class)]
class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','slug','excerpt','body','cover_image','cover_emoji',
        'cover_bg','category_id','author_id','status','featured',
        'breaking','views','read_time','tags','meta_title','meta_desc','published_at',
    ];

    protected $casts = [
        'featured'     => 'boolean',
        'breaking'     => 'boolean',
        'tags'         => 'array',
        'published_at' => 'datetime',
    ];

    /* ── Mutators ──────────────────────────────────────── */
    /**
     * Sanitize rich-text body on write. This is the single chokepoint for
     * stored-XSS defense: every write path (admin controller, seeder, jobs)
     * runs through here, and the view can safely render {!! $article->body !!}.
     */
    public function setBodyAttribute(?string $value): void
    {
        $this->attributes['body'] = $value === null || $value === ''
            ? $value
            : Purifier::clean($value, 'article');
    }

    /* ── Relationships ─────────────────────────────────── */
    public function category() { return $this->belongsTo(Category::class); } // primary (URL/canonical)
    public function author()   { return $this->belongsTo(User::class, 'author_id'); }

    /** Additional categories (beyond the primary) via the article_category pivot. */
    public function categories() { return $this->belongsToMany(Category::class); }

    /* ── Scopes ────────────────────────────────────────── */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
                 ->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
    }

    /**
     * Canonical "is this publicly visible?" check — the row-level mirror of
     * scopePublished(). Use this for access guards and view counting so a
     * future-scheduled post (status=published, published_at in the future)
     * is never reachable by slug or counted.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lessThanOrEqualTo(now());
    }

    /** Marked published but dated in the future — live automatically once that time passes. */
    public function isScheduled(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isFuture();
    }

    public function scopeScheduled(Builder $q): Builder
    {
        return $q->where('status', 'published')->where('published_at', '>', now());
    }

    public function scopeInCategory(Builder $q, string $slug): Builder
    {
        return $q->whereHas('category', fn($c) => $c->where('slug', $slug));
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        $term = trim($term);
        if ($term === '') {
            return $q;
        }

        // MySQL: use the FULLTEXT index (indexed, searches the body too).
        // Strip boolean operators (+ - * " ( ) ~ < > @) from user input first —
        // otherwise "Jaipur -Patna" would silently EXCLUDE Patna and a stray
        // quote would return nothing. Then prefix-match each remaining word.
        if (DB::connection()->getDriverName() === 'mysql') {
            $cleaned = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $term);
            $boolean = collect(preg_split('/\s+/', trim($cleaned)))
                ->filter()
                ->map(fn ($word) => $word . '*')
                ->implode(' ');

            if ($boolean === '') {
                return $q->whereRaw('1 = 0'); // nothing searchable after stripping
            }

            return $q->whereFullText(['title', 'excerpt', 'body'], $boolean, ['mode' => 'boolean']);
        }

        // SQLite / other: LIKE fallback (now including the body).
        return $q->where(fn ($x) => $x
            ->where('title',   'like', "%{$term}%")
            ->orWhere('excerpt', 'like', "%{$term}%")
            ->orWhere('body',  'like', "%{$term}%"));
    }

    /* ── Accessors ─────────────────────────────────────── */
    public function getFormattedDateAttribute(): string
    {
        return ($this->published_at ?? $this->created_at)?->format('d M Y') ?? '';
    }

    public function getTagsListAttribute(): string
    {
        return is_array($this->tags) ? implode(', ', $this->tags) : ($this->tags ?? '');
    }

    /* ── Helpers ───────────────────────────────────────── */
    public static function generateSlug(string $title): string
    {
        $base  = Str::slug($title);
        $slug  = $base;
        $count = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $count++;
        }
        return $slug;
    }

    public static function calculateReadTime(string $body): string
    {
        $words = str_word_count(strip_tags($body));
        return max(1, (int) ceil($words / 200)) . ' min';
    }

    /**
     * Related posts ranked by shared-tag overlap, topped up with same-category
     * posts when there aren't enough tag matches. Falls back to category-only
     * when the article has no tags.
     */
    public function getRelated(int $limit = 3)
    {
        $tags = array_values(array_filter(is_array($this->tags) ? $this->tags : []));
        $base = static::published()->where('id', '!=', $this->id);

        if ($tags) {
            $tagMatches = (clone $base)
                ->where(function (Builder $q) use ($tags) {
                    foreach ($tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                })
                ->latest('published_at')
                ->limit(20)
                ->get()
                ->sortByDesc(fn ($a) => count(array_intersect(is_array($a->tags) ? $a->tags : [], $tags)))
                ->take($limit)
                ->values();

            if ($tagMatches->count() >= $limit) {
                return $tagMatches;
            }

            // Top up with same-category posts not already chosen.
            $fill = (clone $base)
                ->where('category_id', $this->category_id)
                ->whereNotIn('id', $tagMatches->pluck('id'))
                ->latest('published_at')
                ->limit($limit - $tagMatches->count())
                ->get();

            return $tagMatches->concat($fill)->values();
        }

        return $base->where('category_id', $this->category_id)
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /** The previous (older) published article, for in-article navigation. */
    public function previousArticle(): ?self
    {
        if ($this->published_at === null) {
            return null; // drafts/unpublished posts have no place in the timeline
        }

        return static::published()
            ->where('published_at', '<', $this->published_at)
            ->orderByDesc('published_at')
            ->first();
    }

    /** The next (newer) published article. */
    public function nextArticle(): ?self
    {
        if ($this->published_at === null) {
            return null;
        }

        return static::published()
            ->where('published_at', '>', $this->published_at)
            ->orderBy('published_at')
            ->first();
    }

    /**
     * Buffer a view instead of writing the hot articles row on every request.
     * A scheduled command (app:flush-article-views) folds these into
     * articles.views without bumping updated_at. Uses update-or-insert so it
     * stays atomic and driver-agnostic (MySQL + SQLite).
     */
    public function incrementViews(): void
    {
        $updated = DB::table('article_view_buffer')
            ->where('article_id', $this->id)
            ->update(['count' => DB::raw('count + 1')]);

        if ($updated === 0) {
            try {
                DB::table('article_view_buffer')->insert([
                    'article_id' => $this->id,
                    'count'      => 1,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // Lost the insert race — the row now exists, so increment it.
                DB::table('article_view_buffer')
                    ->where('article_id', $this->id)
                    ->update(['count' => DB::raw('count + 1')]);
            }
        }
    }
}
