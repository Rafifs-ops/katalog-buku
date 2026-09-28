<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to the login page', function () {
    $this->get(route('categories.index'))->assertRedirect(route('login'));
});

it('forbids regular users from listing categories', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('categories.index'))->assertForbidden();
});

it('forbids regular users from storing a category', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('categories.store'), ['name' => 'Kategori Baru'])->assertForbidden();

    $this->assertDatabaseMissing('categories', ['name' => 'Kategori Baru']);
});

it('lets a super admin list categories with book count', function () {
    $this->actingAs(User::factory()->superAdmin()->create());

    $category = Category::factory()->create(['name' => 'Teknologi']);
    Book::factory()->count(2)->for($category)->create();

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Teknologi')
        ->assertSee('<td>2</td>', false);
});

it('lets a super admin store a category', function () {
    $this->actingAs(User::factory()->superAdmin()->create());

    $this->post(route('categories.store'), ['name' => 'Kategori Baru'])
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseHas('categories', ['name' => 'Kategori Baru']);
});

it('rejects storing a category with a short name', function () {
    $this->actingAs(User::factory()->superAdmin()->create());

    $this->from(route('categories.create'))
        ->post(route('categories.store'), ['name' => 'AB'])
        ->assertRedirect(route('categories.create'))
        ->assertSessionHasErrors('name');
});

it('rejects a duplicate category name', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    Category::factory()->create(['name' => 'Fiksi']);

    $this->post(route('categories.store'), ['name' => 'Fiksi'])
        ->assertSessionHasErrors('name');
});

it('lets a super admin rename a category', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $category = Category::factory()->create(['name' => 'Fiksi']);

    $this->put(route('categories.update', $category), ['name' => 'Novel'])
        ->assertRedirect(route('categories.index'));

    expect($category->refresh()->name)->toBe('Novel');
});

it('allows keeping the same name when updating a category', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $category = Category::factory()->create(['name' => 'Sains']);

    $this->put(route('categories.update', $category), ['name' => 'Sains'])
        ->assertRedirect(route('categories.index'));

    expect($category->refresh()->name)->toBe('Sains');
});

it('lets a super admin delete a category without books', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $category = Category::factory()->create();

    $this->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'));

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('blocks deleting a category that still has books', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $category = Category::factory()->create();
    Book::factory()->for($category)->create();

    $this->delete(route('categories.destroy', $category))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});
