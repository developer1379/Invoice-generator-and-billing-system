<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can see privacy policy page', function () {
    $response = $this->get(route('privacy'));

    $response->assertSuccessful();
    $response->assertSee('Privacy Policy');
    $response->assertSee('Security & Trust', false);
});

test('guests can see terms of service page', function () {
    $response = $this->get(route('terms'));

    $response->assertSuccessful();
    $response->assertSee('Terms of Service');
    $response->assertSee('Agreement & Rules', false);
});
