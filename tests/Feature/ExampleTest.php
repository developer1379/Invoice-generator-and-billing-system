<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can see marketing page on home route', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Invoicify');
});

test('guests are redirected to login from invoices index', function () {
    $response = $this->get('/invoices');

    $response->assertRedirect(route('login'));
});

test('logged in users see dashboard on invoices index', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/invoices');

    $response->assertSuccessful();
});
