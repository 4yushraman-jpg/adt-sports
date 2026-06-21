<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = rtrim(fake()->sentence(), '.');

        return [
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 1_000_000),
            'excerpt'      => fake()->sentence(),
            'body'         => '<p>' . fake()->paragraph() . '</p>',
            'author_id'    => User::factory(),
            'category_id'  => null,
            'status'       => 'published',
            'featured'     => false,
            'breaking'     => false,
            'tags'         => ['kabaddi', 'pkl'],
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft', 'published_at' => null]);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published', 'published_at' => now()->subDay()]);
    }

    /** status=published but dated in the future — must NOT be publicly visible. */
    public function scheduled(): static
    {
        return $this->state(['status' => 'published', 'published_at' => now()->addWeek()]);
    }
}
