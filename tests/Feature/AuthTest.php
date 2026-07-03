<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('user can view login form', function () {
    $response = $this->get(route('login'));

    $response->assertSuccessful();
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertRedirect(route('invoices.index'));
    $this->assertAuthenticatedAs($user);
});

test('user can view registration form', function () {
    $response = $this->get(route('register'));

    $response->assertSuccessful();
});

test('user can register a new account', function () {
    $response = $this->post(route('register'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('invoices.index'));
    $this->assertAuthenticated();
});

test('authenticated user can view profile settings', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertSuccessful();
    $response->assertViewHas('user');
});

test('authenticated user can update business settings', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update'), [
        'name' => 'Jane Updated',
        'email' => $user->email,
        'business_name' => 'Jane Billing LLC',
        'business_email' => 'billing@jane.com',
        'business_phone' => '+1 (555) 999-9999',
        'business_address' => '789 corporate ln',
        'business_tax_id' => 'VAT-999',
        'business_website' => 'www.jane.com',
        'business_currency' => 'EUR',
        'business_tax_rate' => 15.00,
        'business_discount_rate' => 5.00,
        'business_notes' => 'Some default notes',
        'business_terms' => 'Some default terms',
    ]);

    $response->assertRedirect(route('profile.edit'));
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Jane Updated',
        'business_name' => 'Jane Billing LLC',
        'business_tax_id' => 'VAT-999',
        'business_currency' => 'EUR',
        'business_tax_rate' => 15.00,
        'business_discount_rate' => 5.00,
        'business_notes' => 'Some default notes',
        'business_terms' => 'Some default terms',
    ]);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});

test('redirectToGoogle redirects to Google OAuth endpoint', function () {
    $response = $this->get(route('auth.google'));

    $response->assertRedirect();
    $this->assertStringStartsWith('https://accounts.google.com/o/oauth2/v2/auth', $response->getTargetUrl());
});

test('handleGoogleCallback registers and logs in a new user', function () {
    \Illuminate\Support\Facades\Http::fake([
        'https://oauth2.googleapis.com/token' => \Illuminate\Support\Facades\Http::response([
            'access_token' => 'mock-google-token',
        ]),
        'https://www.googleapis.com/oauth2/v3/userinfo' => \Illuminate\Support\Facades\Http::response([
            'email' => 'oauth-user@example.com',
            'name' => 'OAuth User',
            'sub' => '1234567890',
        ]),
    ]);

    $response = $this->get(route('auth.google.callback', ['code' => 'mock-code']));

    $response->assertRedirect(route('invoices.index'));
    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'oauth-user@example.com',
        'google_id' => '1234567890',
        'name' => 'OAuth User',
    ]);
});

test('handleGoogleCallback links existing user and logs in', function () {
    $user = User::factory()->create([
        'email' => 'existing-user@example.com',
        'google_id' => null,
    ]);

    \Illuminate\Support\Facades\Http::fake([
        'https://oauth2.googleapis.com/token' => \Illuminate\Support\Facades\Http::response([
            'access_token' => 'mock-google-token',
        ]),
        'https://www.googleapis.com/oauth2/v3/userinfo' => \Illuminate\Support\Facades\Http::response([
            'email' => 'existing-user@example.com',
            'name' => 'Existing User',
            'sub' => '987654321',
        ]),
    ]);

    $response = $this->get(route('auth.google.callback', ['code' => 'mock-code']));

    $response->assertRedirect(route('invoices.index'));
    $this->assertAuthenticatedAs($user);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'google_id' => '987654321',
    ]);
});

test('new registration sends verification email', function () {
    \Illuminate\Support\Facades\Notification::fake();

    $response = $this->post(route('register'), [
        'name' => 'Jane Doe',
        'email' => 'jane.verify@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('invoices.index'));
    $this->assertAuthenticated();

    $user = User::where('email', 'jane.verify@example.com')->first();
    expect($user->email_verified_at)->toBeNull();

    \Illuminate\Support\Facades\Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
});

test('unverified user cannot access invoices index dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('invoices.index'));

    $response->assertRedirect(route('verification.notice'));
});

test('unverified user cannot access profile page', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertRedirect(route('verification.notice'));
});

test('user can verify their email using signed link', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('invoices.index'));
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});


