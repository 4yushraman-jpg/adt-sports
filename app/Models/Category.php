<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','color','description','article_count'];

    public const CACHE_KEY = 'categories.all';

    protected static function booted(): void
    {
        // The category list renders on every public page; bust the cache on any change.
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public function articles() { return $this->hasMany(Article::class); }

    /** Cached, alphabetically-ordered list for nav/sidebars. */
    public static function ordered()
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::orderBy('name')->get());
    }

    public function refreshCount(): void
    {
        $this->update([
            'article_count' => $this->articles()->where('status','published')->count()
        ]);
    }
}
