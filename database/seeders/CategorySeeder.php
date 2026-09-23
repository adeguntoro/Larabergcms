<?php

namespace LarabergCms\LarabergCms\Database\Seeders;

use Illuminate\Database\Seeder;
use LarabergCms\LarabergCms\Models\Category;

/**
 * Seeds dummy categories for local development and demos.
 *
 * Categories are matched on `slug`, so the seeder is idempotent: running it
 * again updates the existing rows instead of creating duplicates.
 *
 *     php artisan migrate
 *     php artisan db:seed --class="LarabergCms\LarabergCms\Database\Seeders\CategorySeeder"
 */
class CategorySeeder extends Seeder
{
    /**
     * The dummy categories that will be seeded.
     *
     * @var array<int, array{name: string, slug: string}>
     */
    protected array $categories = [
        ['name' => 'Uncategorized', 'slug' => 'uncategorized'],
        ['name' => 'Announcements', 'slug' => 'announcements'],
        ['name' => 'News', 'slug' => 'news'],
        ['name' => 'Tutorials', 'slug' => 'tutorials'],
        ['name' => 'Technology', 'slug' => 'technology'],
        ['name' => 'Design', 'slug' => 'design'],
        ['name' => 'Business', 'slug' => 'business'],
        ['name' => 'Marketing', 'slug' => 'marketing'],
        ['name' => 'Travel', 'slug' => 'travel'],
        ['name' => 'Food & Drink', 'slug' => 'food-and-drink'],
        ['name' => 'Health & Fitness', 'slug' => 'health-and-fitness'],
        ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }

        if (isset($this->command)) {
            $this->command->info(sprintf(
                'Seeded %d categories.',
                count($this->categories),
            ));
        }
    }
}
