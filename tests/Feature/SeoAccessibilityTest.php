<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_has_skip_link_and_main_landmark(): void
    {
        $response = $this->get('/');

        $response->assertSee('class="skip-link"', false);
        $response->assertSee('href="#main"', false);
        $response->assertSee('<main id="main">', false);
    }

    public function test_nav_has_aria_label(): void
    {
        $this->get('/')->assertSee('aria-label="Primary"', false);
    }

    public function test_first_list_page_self_canonicalizes_without_page_param(): void
    {
        Article::factory()->count(3)->published()->create();

        $response = $this->get('/');
        // No ?page= on page 1 canonical.
        $response->assertSee('<link rel="canonical" href="' . url('/') . '">', false);
    }

    public function test_paginated_page_canonical_includes_page_number(): void
    {
        // articles_per_page defaults to 10; make enough for a page 2.
        Article::factory()->count(13)->published()->create();

        $response = $this->get('/?page=2');
        $response->assertSee('?page=2"', false); // canonical carries the page
        $response->assertSee('rel="prev"', false); // prev link present on page 2
    }

    public function test_page_one_emits_next_but_not_prev(): void
    {
        Article::factory()->count(13)->published()->create();

        $response = $this->get('/');
        $response->assertSee('rel="next"', false);
        $response->assertDontSee('rel="prev"', false);
    }
}
