<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Blog::factory(1)->create([
            'title' => fake()->name(),
            'description' => fake()->text(),
            'user_id' => 1,
            'parent_category' => 1,
            'child_category' => 1,
            'tag' => 1,
            'slug' => fake()->slug(),
            'type' => 'publish',
            'photo' => 'https://cdn-icons-png.flaticon.com/512/4123/4123763.png',
        ]);
    }
}
