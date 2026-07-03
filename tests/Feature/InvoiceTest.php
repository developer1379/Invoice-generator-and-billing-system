<?php

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('user can view list of invoices on index', function () {
    Invoice::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->get(route('invoices.index'));

    $response->assertSuccessful();
    $response->assertViewHas('invoices');
});

test('user can open creation page', function () {
    $response = $this->get(route('invoices.create'));

    $response->assertSuccessful();
    $response->assertViewHas('defaultInvoice');
});

test('user can store a new invoice', function () {
    $invoiceData = Invoice::factory()->raw(['user_id' => $this->user->id]);

    $response = $this->post(route('invoices.store'), $invoiceData);

    $invoice = Invoice::latest()->first();

    $response->assertRedirect(route('invoices.show', $invoice));
    $this->assertDatabaseHas('invoices', [
        'invoice_number' => $invoiceData['invoice_number'],
        'sender_name' => $invoiceData['sender_name'],
        'user_id' => $this->user->id,
    ]);
});

test('user can view single invoice details', function () {
    $invoice = Invoice::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('invoices.show', $invoice));

    $response->assertSuccessful();
    $response->assertViewHas('invoice');
});

test('user can view edit page of invoice', function () {
    $invoice = Invoice::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('invoices.edit', $invoice));

    $response->assertSuccessful();
    $response->assertViewHas('invoice');
    $response->assertViewHas('defaultInvoice');
});

test('user can update invoice', function () {
    $invoice = Invoice::factory()->create(['user_id' => $this->user->id]);
    $newData = Invoice::factory()->raw([
        'user_id' => $this->user->id,
        'invoice_number' => 'INV-UPDATED-123',
    ]);

    $response = $this->put(route('invoices.update', $invoice), $newData);

    $response->assertRedirect(route('invoices.show', $invoice));

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'invoice_number' => 'INV-UPDATED-123',
    ]);
});

test('user can delete invoice', function () {
    $invoice = Invoice::factory()->create(['user_id' => $this->user->id]);

    $response = $this->delete(route('invoices.destroy', $invoice));

    $response->assertRedirect(route('invoices.index'));

    $this->assertDatabaseMissing('invoices', [
        'id' => $invoice->id,
    ]);
});

test('guest can open creation page', function () {
    Auth::logout();

    $response = $this->get(route('invoices.create'));

    $response->assertSuccessful();
    $response->assertViewHas('defaultInvoice');
});

test('guest can store a new invoice and track in session', function () {
    Auth::logout();
    $invoiceData = Invoice::factory()->raw(['user_id' => null]);

    $response = $this->post(route('invoices.store'), $invoiceData);

    $invoice = Invoice::latest()->first();

    $response->assertRedirect(route('invoices.show', $invoice));
    $this->assertDatabaseHas('invoices', [
        'invoice_number' => $invoiceData['invoice_number'],
        'sender_name' => $invoiceData['sender_name'],
        'user_id' => null,
    ]);

    $response->assertSessionHas('guest_invoices', [$invoice->id]);
});

test('registered user claims guest invoices upon registration', function () {
    Auth::logout();
    $invoice = Invoice::factory()->create(['user_id' => null]);
    session(['guest_invoices' => [$invoice->id]]);

    $response = $this->post(route('register'), [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect(route('invoices.index'));

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'user_id' => User::where('email', 'john.doe@example.com')->first()->id,
    ]);

    $this->assertNull(session('guest_invoices'));
});

test('logged in user claims guest invoices upon login', function () {
    Auth::logout();
    $user = User::factory()->create(['password' => Hash::make('Password123!')]);
    $invoice = Invoice::factory()->create(['user_id' => null]);
    session(['guest_invoices' => [$invoice->id]]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'Password123!',
    ]);

    $response->assertRedirect(route('invoices.index'));

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'user_id' => $user->id,
    ]);

    $this->assertNull(session('guest_invoices'));
});
