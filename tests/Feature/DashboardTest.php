<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('shows dashboard widgets with entity counts for the current user', function () {
    User::factory()->count(2)->create();
    Category::factory()->count(3)->create();
    Book::factory()->count(4)->for($this->user)->create();
    Book::factory()->count(2)->create();

    $this->get(route('admin'))
        ->assertOk()
        ->assertSee('Total Buku')
        ->assertDontSee('Total Users')
        ->assertSee((string) $this->user->books()->count());
});

it('does not show books owned by other users', function () {
    Category::factory()->create(['name' => 'Publik']);
    $otherBook = Book::factory()->create([
        'title' => 'Buku Milik User Lain yang disembunyikan',
    ]);

    $this->get(route('admin'))
        ->assertOk()
        ->assertDontSee($otherBook->title);
});

it('shows the books in one card with cover, title and category', function () {
    $category = Category::factory()->create(['name' => 'Teknologi']);
    $book = Book::factory()->for($this->user)->for($category)->create([
        'cover_url' => 'covers/dashboard.jpg',
    ]);

    $this->get(route('admin'))
        ->assertOk()
        ->assertSee('Daftar Buku')
        ->assertSee($book->title)
        ->assertSee('Teknologi')
        ->assertSee('storage/covers/dashboard.jpg', false);
});

it('shows all of the user books in the single list', function () {
    $category = Category::factory()->create();

    $books = Book::factory()->count(5)->for($this->user)->for($category)->create();

    $response = $this->get(route('admin'))->assertOk();

    foreach ($books as $book) {
        $response->assertSee($book->title);
    }
});

it('shows an empty state when the user has no books', function () {
    $this->get(route('admin'))
        ->assertOk()
        ->assertSee('Belum ada buku pada daftar ini.');
});
