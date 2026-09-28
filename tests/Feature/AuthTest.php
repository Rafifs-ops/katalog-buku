<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('shows the login form', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('email')
        ->assertSee('password');
});

it('redirects guests away from admin pages to the login page', function () {
    $this->get(route('admin'))->assertRedirect(route('login'));
    $this->get(route('books.index'))->assertRedirect(route('login'));
    $this->post(route('books.store'), [])->assertRedirect(route('login'));
});

it('returns 404 for the removed user crud pages', function () {
    $this->get('/users')->assertNotFound();
});

it('redirects guests away from api user to json 401', function () {
    $this->getJson('api/user')->assertUnauthorized();
});

it('authenticates a user with valid credentials', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect(route('admin'));

    $this->assertAuthenticatedAs($user, 'web');
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('redirects authenticated users away from the login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('admin'));
});

it('logs the user out and invalidates the session', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest('web');
});

it('shows the register form', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('name')
        ->assertSee('password_confirmation')
        ->assertSee(route('login'));
});

it('registers a new user and authenticates them', function () {
    $this->post(route('register'), [
        'name' => 'Pengguna Baru',
        'email' => 'baru@example.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertRedirect(route('admin'));

    $user = User::where('email', 'baru@example.com')->firstOrFail();

    expect(Hash::check('rahasia123', $user->password))->toBeTrue()
        ->and($user->role)->toBe(User::ROLE_GUEST);

    $this->assertAuthenticatedAs($user, 'web');
});

it('validates the registration input', function () {
    $this->from(route('register'))
        ->post(route('register'), [])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['name', 'email', 'password']);

    expect(User::count())->toBe(0);
    $this->assertGuest();
});

it('rejects a mismatched password confirmation', function () {
    $this->post(route('register'), [
        'name' => 'Pengguna Baru',
        'email' => 'baru@example.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'berbeda',
    ])->assertSessionHasErrors('password');

    expect(User::count())->toBe(0);
});

it('rejects a duplicate email', function () {
    $existing = User::factory()->create();

    $this->post(route('register'), [
        'name' => 'Pengguna Baru',
        'email' => $existing->email,
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('email');

    expect(User::count())->toBe(1);
});

it('redirects authenticated users away from the register page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('register'))
        ->assertRedirect(route('admin'));
});
