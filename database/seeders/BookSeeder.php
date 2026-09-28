<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        User::all()->each(function (User $user) use ($categories) {
            $categories->shuffle()->take(3)->each(function (Category $category) use ($user) {
                Book::factory()->for($user)->for($category)->create();
            });
        });
    }
}
