<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_editor_cannot_access_admin_only_users_page(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_access_users_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_editor_cannot_update_another_authors_article(): void
    {
        $stranger = User::factory()->editor()->create();
        $article  = Article::factory()->create(); // owned by a different author

        $this->actingAs($stranger)
            ->put("/admin/articles/{$article->id}", ['title' => 'Hijacked', 'status' => 'draft'])
            ->assertForbidden();
    }

    public function test_author_can_update_their_own_article(): void
    {
        $author  = User::factory()->editor()->create();
        $article = Article::factory()->create(['author_id' => $author->id]);

        $this->actingAs($author)
            ->put("/admin/articles/{$article->id}", ['title' => 'My Edit', 'status' => 'draft'])
            ->assertRedirect();

        $this->assertSame('My Edit', $article->fresh()->title);
    }

    public function test_admin_can_update_any_article(): void
    {
        $admin   = User::factory()->admin()->create();
        $article = Article::factory()->create();

        $this->actingAs($admin)
            ->put("/admin/articles/{$article->id}", ['title' => 'Admin Edit', 'status' => 'draft'])
            ->assertRedirect();

        $this->assertSame('Admin Edit', $article->fresh()->title);
    }

    public function test_editor_cannot_delete_users(): void
    {
        $editor = User::factory()->editor()->create();
        $victim = User::factory()->create();

        $this->actingAs($editor)
            ->delete("/admin/users/{$victim->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $victim->id]);
    }
}
