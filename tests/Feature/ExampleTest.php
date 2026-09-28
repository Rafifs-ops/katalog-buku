<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the admin dashboard redirects guests to the login page', function () {
    $response = $this->get(route('admin'));

    $response->assertRedirect(route('login'));
});
