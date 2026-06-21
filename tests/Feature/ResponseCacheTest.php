<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Support\PublicResponseCacheProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\ResponseCache\Facades\ResponseCache;
use Tests\TestCase;

class ResponseCacheTest extends TestCase
{
    use RefreshDatabase;

    /* ── Security: the cache profile gate ──────────────────────────── */

    public function test_profile_never_caches_authenticated_requests(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $profile = new PublicResponseCacheProfile();
        $this->assertFalse($profile->shouldCacheRequest(Request::create('/', 'GET')));
    }

    public function test_profile_never_caches_admin_beacon_search_or_non_get(): void
    {
        $profile = new PublicResponseCacheProfile();

        $this->assertFalse($profile->shouldCacheRequest(Request::create('/admin', 'GET')));
        $this->assertFalse($profile->shouldCacheRequest(Request::create('/admin/articles', 'GET')));
        $this->assertFalse($profile->shouldCacheRequest(Request::create('/article/5/hit', 'GET')));
        $this->assertFalse($profile->shouldCacheRequest(Request::create('/search', 'GET')));
        $this->assertFalse($profile->shouldCacheRequest(Request::create('/', 'POST')));
    }

    public function test_profile_caches_public_get_pages(): void
    {
        $profile = new PublicResponseCacheProfile();

        $this->assertTrue($profile->shouldCacheRequest(Request::create('/', 'GET')));
        $this->assertTrue($profile->shouldCacheRequest(Request::create('/article/some-slug', 'GET')));
        $this->assertTrue($profile->shouldCacheRequest(Request::create('/category/x', 'GET')));
    }

    public function test_only_200_responses_are_cacheable(): void
    {
        $profile = new PublicResponseCacheProfile();

        $this->assertTrue($profile->shouldCacheResponse(new \Symfony\Component\HttpFoundation\Response('', 200)));
        $this->assertFalse($profile->shouldCacheResponse(new \Symfony\Component\HttpFoundation\Response('', 204)));
        $this->assertFalse($profile->shouldCacheResponse(new \Symfony\Component\HttpFoundation\Response('', 404)));
        $this->assertFalse($profile->shouldCacheResponse(new \Symfony\Component\HttpFoundation\Response('', 302)));
    }

    /* ── Behaviour: cache hit + bust ───────────────────────────────── */

    public function test_public_page_is_cached_and_busted_on_content_change(): void
    {
        config(['responsecache.enabled' => true]);
        ResponseCache::clear();

        $article = Article::factory()->published()->create(['title' => 'Cached Title AAA']);

        // Warm the cache.
        $this->get("/article/{$article->slug}")->assertOk()->assertSee('Cached Title AAA', false);

        // Change the title WITHOUT firing the observer — the cache should still serve the old page.
        DB::table('articles')->where('id', $article->id)->update(['title' => 'Changed Title BBB']);
        $this->get("/article/{$article->slug}")
            ->assertSee('Cached Title AAA', false)
            ->assertDontSee('Changed Title BBB', false);

        // A real model save fires the observer -> ResponseCache::clear() -> fresh render.
        Article::find($article->id)->update(['excerpt' => 'touch to bust']);
        $this->get("/article/{$article->slug}")->assertSee('Changed Title BBB', false);
    }

    public function test_beacon_is_never_cached(): void
    {
        config(['responsecache.enabled' => true]);
        ResponseCache::clear();

        $article = Article::factory()->published()->create(['views' => 0]);

        $this->get(route('article.hit', $article))->assertNoContent();
        $this->get(route('article.hit', $article))->assertNoContent();
        $this->artisan('app:flush-article-views')->assertSuccessful();

        // Both beacons counted => the endpoint was not served from cache.
        $this->assertSame(2, $article->fresh()->views);
    }
}
