<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class Article extends Model
{
    use HasFactory;

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
    public function category() { return $this->belongsTo(Category::class); }
    public function author()   { return $this->belongsTo(User::class, 'author_id'); }

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

    public function scopeInCategory(Builder $q, string $slug): Builder
    {
        return $q->whereHas('category', fn($c) => $c->where('slug', $slug));
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        return $q->where(fn($x) => $x
            ->where('title',   'like', "%{$term}%")
            ->orWhere('excerpt','like', "%{$term}%"));
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

    public function getRelated(int $limit = 3)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where('category_id', $this->category_id)
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
