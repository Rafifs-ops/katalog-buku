<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

function validBookPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Judul Buku yang Cukup Panjang',
        'description' => 'Deskripsi buku yang cukup panjang untuk validasi.',
        'content' => 'Isi konten buku yang cukup panjang untuk validasi form.',
        'category_id' => Category::factory()->create()->id,
        'cover_url' => UploadedFile::fake()->image('cover.jpg'),
    ], $overrides);
}

it('lists books with author and category', function () {
    $book = Book::factory()->for($this->user)->create(['cover_url' => 'covers/example.jpg']);

    $this->get(route('books.index'))
        ->assertOk()
        ->assertSee($book->title)
        ->assertSee($book->user->name)
        ->assertSee($book->category->name)
        ->assertSee('storage/covers/example.jpg', false);
});

it('hides books owned by other users from the list', function () {
    $mine = Book::factory()->for($this->user)->create();
    $theirs = Book::factory()->create();

    $this->get(route('books.index'))
        ->assertOk()
        ->assertSee($mine->title)
        ->assertDontSee($theirs->title);
});

it('shows the create book form without a user picker', function () {
    $this->get(route('books.create'))
        ->assertOk()
        ->assertDontSee('name="user_id"', false);
});

it('stores a valid book with an uploaded cover owned by the authenticated user', function () {
    Storage::fake('public');

    $payload = validBookPayload();

    $this->post(route('books.store'), $payload)
        ->assertRedirect(route('books.index'));

    $book = Book::first();

    expect($book)->not->toBeNull()
        ->and($book->user_id)->toBe(auth()->id())
        ->and($book->category_id)->toBe($payload['category_id'])
        ->and($book->cover_url)->toStartWith('covers/');

    Storage::disk('public')->assertExists($book->cover_url);
});

it('ignores a submitted user_id and always uses the authenticated user', function () {
    Storage::fake('public');

    $otherUser = User::factory()->create();

    $this->post(route('books.store'), validBookPayload([
        'user_id' => $otherUser->id,
    ]))->assertRedirect(route('books.index'));

    expect(Book::first()->user_id)->toBe(auth()->id())
        ->and(Book::first()->user_id)->not->toBe($otherUser->id);
});

it('rejects storing a book with missing fields', function () {
    $this->from(route('books.create'))
        ->post(route('books.store'), [])
        ->assertRedirect(route('books.create'))
        ->assertSessionHasErrors(['title', 'description', 'content', 'cover_url', 'category_id']);

    expect(Book::count())->toBe(0);
});

it('rejects storing a book cover that is not an image', function () {
    $this->post(route('books.store'), validBookPayload([
        'cover_url' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
    ]))->assertSessionHasErrors('cover_url');
});

it('rejects storing a book with unknown category', function () {
    $this->post(route('books.store'), validBookPayload([
        'category_id' => 999,
    ]))->assertSessionHasErrors('category_id');
});

it('shows the edit book form with cover preview and no user picker', function () {
    $book = Book::factory()->for($this->user)->create(['cover_url' => 'covers/current.jpg']);

    $this->get(route('books.edit', $book))
        ->assertOk()
        ->assertSee($book->title)
        ->assertDontSee('name="user_id"', false)
        ->assertSee('storage/covers/current.jpg', false);
});

it('updates a book and can move it to another category', function () {
    Storage::fake('public');

    $book = Book::factory()->for($this->user)->create();
    $originalUserId = $book->user_id;
    $newCategory = Category::factory()->create();

    $this->put(route('books.update', $book), [
        'title' => 'Judul Buku Setelah Diupdate',
        'description' => 'Deskripsi buku setelah diupdate untuk validasi.',
        'content' => 'Isi konten buku setelah diupdate untuk validasi form.',
        'cover_url' => UploadedFile::fake()->image('new-cover.png'),
        'category_id' => $newCategory->id,
    ])->assertRedirect(route('books.index'));

    $book->refresh();

    expect($book->title)->toBe('Judul Buku Setelah Diupdate')
        ->and($book->user_id)->toBe($originalUserId)
        ->and($book->category_id)->toBe($newCategory->id)
        ->and($book->cover_url)->toStartWith('covers/');

    Storage::disk('public')->assertExists($book->cover_url);
});

it('keeps the existing cover when updating without a new file', function () {
    Storage::fake('public');

    $book = Book::factory()->for($this->user)->create(['cover_url' => 'covers/keep-me.jpg']);

    $this->put(route('books.update', $book), [
        'title' => 'Judul Buku Setelah Diupdate',
        'description' => 'Deskripsi buku setelah diupdate untuk validasi.',
        'content' => 'Isi konten buku setelah diupdate untuk validasi form.',
        'cover_url' => null,
        'category_id' => $book->category_id,
    ])->assertRedirect(route('books.index'));

    expect($book->refresh()->cover_url)->toBe('covers/keep-me.jpg');
});

it('deletes a book and its cover file', function () {
    Storage::fake('public');

    $book = Book::factory()->for($this->user)->create(['cover_url' => 'covers/delete-me.jpg']);
    Storage::disk('public')->put('covers/delete-me.jpg', 'image-bytes');

    $this->delete(route('books.destroy', $book))
        ->assertRedirect(route('books.index'));

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
    Storage::disk('public')->assertMissing('covers/delete-me.jpg');
});

it('forbids viewing a book owned by another user', function () {
    $book = Book::factory()->create();

    $this->get(route('books.show', $book))->assertForbidden();
});

it('forbids editing a book owned by another user', function () {
    $book = Book::factory()->create();

    $this->get(route('books.edit', $book))->assertForbidden();
});

it('forbids updating a book owned by another user', function () {
    $book = Book::factory()->create();

    $this->put(route('books.update', $book), [
        'title' => 'Judul Buku yang Dipaksa User Lain',
        'description' => 'Deskripsi buku yang dipaksa diupdate oleh user lain.',
        'content' => 'Isi konten buku yang dipaksa diupdate oleh user lain.',
        'cover_url' => null,
        'category_id' => $book->category_id,
    ])->assertForbidden();

    expect($book->refresh()->title)->not->toBe('Judul Buku yang Dipaksa User Lain');
});

it('forbids deleting a book owned by another user', function () {
    $book = Book::factory()->create();

    $this->delete(route('books.destroy', $book))->assertForbidden();

    $this->assertDatabaseHas('books', ['id' => $book->id]);
});

it('lets a super admin see all books in the list', function () {
    $admin = User::factory()->superAdmin()->create();
    $theirs = Book::factory()->create();
    $mine = Book::factory()->for($this->user)->create();

    $this->actingAs($admin)
        ->get(route('books.index'))
        ->assertOk()
        ->assertSee($theirs->title)
        ->assertSee($theirs->user->name)
        ->assertSee($mine->title);
});

it('lets a super admin open a read-only detail page for another user book', function () {
    $admin = User::factory()->superAdmin()->create();
    $book = Book::factory()->create();

    $this->actingAs($admin)
        ->get(route('books.show', $book))
        ->assertOk()
        ->assertSee($book->title)
        ->assertSee($book->user->name)
        ->assertSee('Kembali');
});

it('redirects the owner from the show page to the edit form', function () {
    $book = Book::factory()->for($this->user)->create();

    $this->get(route('books.show', $book))
        ->assertRedirect(route('books.edit', $book));
});

it('lets a super admin still edit their own book', function () {
    $admin = User::factory()->superAdmin()->create();
    $book = Book::factory()->for($admin)->create();

    $this->actingAs($admin)
        ->get(route('books.edit', $book))
        ->assertOk()
        ->assertSee($book->title);
});

it('forbids a super admin from editing a book owned by another user', function () {
    $admin = User::factory()->superAdmin()->create();
    $book = Book::factory()->create();

    $this->actingAs($admin)
        ->get(route('books.edit', $book))
        ->assertForbidden();
});

it('forbids a super admin from updating a book owned by another user', function () {
    $admin = User::factory()->superAdmin()->create();
    $book = Book::factory()->create(['title' => 'Judul Asli Milik User Lain']);

    $this->actingAs($admin)
        ->put(route('books.update', $book), [
            'title' => 'Judul Yang Dipaksa Super Admin',
            'description' => 'Deskripsi paksaan super admin yang cukup panjang.',
            'content' => 'Isi konten paksaan super admin yang cukup panjang.',
            'cover_url' => null,
            'category_id' => $book->category_id,
        ])
        ->assertForbidden();

    expect($book->refresh()->title)->toBe('Judul Asli Milik User Lain');
});

it('lets one user own many books and keeps each book on one user and one category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    Book::factory()->count(3)->for($user)->for($category)->create();

    expect($user->books)->toHaveCount(3)
        ->and($user->books->pluck('user_id')->unique())->toHaveCount(1)
        ->and($user->books->pluck('category_id')->unique())->toHaveCount(1)
        ->and($category->books)->toHaveCount(3);
});
