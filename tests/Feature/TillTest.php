<?php

use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

uses(LazilyRefreshDatabase::class);

test('guest can open till index view', function () {
    $response = $this->get(route('till.index'));

    $response->assertSuccessful();
    $response->assertViewHas('products');
});

test('authenticated user can open till index view', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('till.index'));

    $response->assertSuccessful();
    $response->assertViewHas('products');
});

test('guest can checkout from till and track invoice in session', function () {
    $response = $this->post(route('till.store'), [
        'client_name' => 'POS Client',
        'client_email' => 'client@pos.com',
        'client_phone' => '12345',
        'client_address' => 'POS Street 12',
        'client_company' => 'POS Corp',
        'client_vat' => 'VAT-POS',
        'currency' => 'USD',
        'tax_rate' => 10.00,
        'discount_rate' => 5.00,
        'shipping_cost' => 15.00,
        'theme_color' => '#4f46e5',
        'template_id' => 'blueprint',
        'cart_items' => [
            [
                'name' => 'Web Design Consulting',
                'quantity' => 2,
                'price' => 150.00,
                'tax_rate' => 10.00,
            ]
        ],
    ]);

    $invoice = Invoice::latest()->first();

    $response->assertRedirect(route('invoices.show', $invoice));
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'user_id' => null,
        'client_name' => 'POS Client',
    ]);
    
    $this->assertEquals(328.50, (float) $invoice->total);
    $this->assertContains($invoice->id, session('guest_invoices', []));
});

test('authenticated user can checkout from till', function () {
    $user = User::factory()->create([
        'business_name' => 'ACME HQ',
        'business_email' => 'billing@acme.com',
    ]);
    
    $response = $this->actingAs($user)->post(route('till.store'), [
        'client_name' => 'POS Client',
        'client_email' => 'client@pos.com',
        'client_phone' => '12345',
        'client_address' => 'POS Street 12',
        'client_company' => 'POS Corp',
        'client_vat' => 'VAT-POS',
        'currency' => 'USD',
        'tax_rate' => 10.00,
        'discount_rate' => 5.00,
        'shipping_cost' => 15.00,
        'theme_color' => '#4f46e5',
        'template_id' => 'blueprint',
        'cart_items' => [
            [
                'name' => 'Web Design Consulting',
                'quantity' => 2,
                'price' => 150.00,
                'tax_rate' => 10.00,
            ]
        ],
    ]);

    $invoice = Invoice::latest()->first();

    $response->assertRedirect(route('invoices.show', $invoice));
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'user_id' => $user->id,
        'sender_name' => 'ACME HQ',
        'sender_email' => 'billing@acme.com',
    ]);
    
    $this->assertEquals(328.50, (float) $invoice->total);
});

test('guest cannot access product management routes', function () {
    $this->get(route('products.index'))->assertRedirect(route('login'));
    $this->post(route('products.store'), [])->assertRedirect(route('login'));
});

test('authenticated user can manage products', function () {
    $user = User::factory()->create();
    
    // Store
    $response = $this->actingAs($user)->post(route('products.store'), [
        'sku' => 'TEST-001',
        'name' => 'Test Product',
        'description' => 'A description of test product.',
        'price' => 99.99,
        'tax_rate' => 15.00,
        'image_url' => 'https://example.com/item.jpg',
        'custom_fields' => ['Color' => 'Black', 'Size' => 'Large'],
    ]);
    
    $product = Product::latest()->first();
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'user_id' => $user->id,
        'sku' => 'TEST-001',
        'name' => 'Test Product',
        'image_url' => 'https://example.com/item.jpg',
    ]);
    
    $this->assertEquals(['Color' => 'Black', 'Size' => 'Large'], $product->custom_fields);
    
    // Update
    $response = $this->actingAs($user)->put(route('products.update', $product), [
        'sku' => 'TEST-001-MOD',
        'name' => 'Test Product Mod',
        'price' => 89.99,
        'tax_rate' => 10.00,
        'image_url' => 'https://example.com/item-mod.jpg',
        'custom_fields' => ['Color' => 'White', 'Size' => 'Medium'],
    ]);
    
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'sku' => 'TEST-001-MOD',
        'name' => 'Test Product Mod',
        'image_url' => 'https://example.com/item-mod.jpg',
    ]);
    
    $this->assertEquals(89.99, (float) Product::find($product->id)->price);
    $this->assertEquals(['Color' => 'White', 'Size' => 'Medium'], Product::find($product->id)->custom_fields);
    
    // Destroy
    $response = $this->actingAs($user)->delete(route('products.destroy', $product));
    
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});

test('product controller dynamically validates product custom fields settings schema', function () {
    $user = User::factory()->create([
        'settings' => [
            'product_custom_fields' => [
                ['name' => 'Warranty', 'type' => 'number', 'required' => true, 'min' => 12, 'max' => 60],
            ],
        ],
    ]);

    // Validation fails when 'Warranty' is missing
    $response = $this->actingAs($user)->post(route('products.store'), [
        'sku' => 'TEST-002',
        'name' => 'Test Product Missing Warranty',
        'price' => 10.00,
        'tax_rate' => 0.00,
    ]);
    $response->assertSessionHasErrors(['custom_fields.Warranty']);

    // Validation fails when 'Warranty' is less than min (12)
    $response = $this->actingAs($user)->post(route('products.store'), [
        'sku' => 'TEST-002',
        'name' => 'Test Product Low Warranty',
        'price' => 10.00,
        'tax_rate' => 0.00,
        'custom_fields' => ['Warranty' => 6],
    ]);
    $response->assertSessionHasErrors(['custom_fields.Warranty']);

    // Validation passes when 'Warranty' is in range (12-60)
    $response = $this->actingAs($user)->post(route('products.store'), [
        'sku' => 'TEST-002',
        'name' => 'Test Product Good Warranty',
        'price' => 10.00,
        'tax_rate' => 0.00,
        'custom_fields' => ['Warranty' => 24],
    ]);
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'name' => 'Test Product Good Warranty',
    ]);
});

test('authenticated user can upload image via imgbb proxy route', function () {
    $user = User::factory()->create();

    // Fake the external ImgBB API response
    Http::fake([
        'https://api.imgbb.com/*' => Http::response([
            'data' => [
                'url' => 'https://i.ibb.co/xyz123/product.png'
            ]
        ], 200)
    ]);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($user)->post(route('imgbb.upload'), [
        'image' => $file,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'url' => 'https://i.ibb.co/xyz123/product.png'
        ]
    ]);
});
