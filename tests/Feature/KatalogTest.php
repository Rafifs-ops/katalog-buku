<?php

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists books publicly without requiring login', function () {
    $category = Category::factory()->create(['name' => 'Fiksi']);
    $book = Book::factory()->for($category)->create(['cover_url' => 'covers/katalog.jpg']);

    $this->get(route('katalog.index'))
        ->assertOk()
        ->assertSee($book->title)
        ->assertSee($book->user->name)
        ->assertSee($book->category->name)
        ->assertSee('storage/covers/katalog.jpg', false);

    $this->assertGuest();
});

it('shows an empty state when there are no books', function () {
    $this->get(route('katalog.index'))
        ->assertOk()
        ->assertSee('Belum ada buku yang dipublikasikan.');
});

it('shows the public detail page of a book', function () {
    $book = Book::factory()->create([
        'description' => 'Deskripsi panjang untuk halaman katalog publik.',
        'content' => 'Isi lengkap buku yang tampil pada halaman detail katalog.',
    ]);

    $this->get(route('katalog.show', $book))
        ->assertOk()
        ->assertSee($book->title)
        ->assertSee($book->user->name)
        ->assertSee($book->category->name)
        ->assertSee($book->description)
        ->assertSee('Isi lengkap buku yang tampil pada halaman detail katalog.')
        ->assertSee(route('katalog.index'));
});

it('returns 404 for an unknown book on the detail page', function () {
    $this->get(route('katalog.show', 999))->assertNotFound();
});
