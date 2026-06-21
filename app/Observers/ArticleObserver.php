<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;

class ArticleObserver
{
    public function saved(Article $article): void
    {
        $this->refreshCategoryCounts($article);
        $this->flushSeoCache();
    }

    public function deleted(Article $article): void
    {
        $this->refreshCategoryCounts($article);
        $this->flushSeoCache();
    }

    public function restored(Article $article): void
    {
        $this->refreshCategoryCounts($article);
        $this->flushSeoCache();
    }

    public function forceDeleted(Article $article): void
    {
        $this->flushSeoCache();
    }

    /** Keep the denormalised published-article counts in sync for the current and previous category. */
    private function refreshCategoryCounts(Article $article): void
    {
        if ($article->category_id) {
            Category::find($article->category_id)?->refreshCount();
        }

        $original = $article->getOriginal('category_id');
        if ($original && $original !== $article->category_id) {
            Category::find($original)?->refreshCount();
        }
    }

    private function flushSeoCache(): void
    {
        Cache::forget('seo.sitemap');
        Cache::forget('seo.news_sitemap');
        Cache::forget('seo.feed');

        // Content changed — invalidate cached full-page responses.
        ResponseCache::clear();
    }
}
