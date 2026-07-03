<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TillController extends Controller
{
    /**
     * Display the POS Till interface.
     */
    public function index(): View
    {
        if (Auth::check()) {
            $products = Product::where('user_id', Auth::id())->latest()->get();
        } else {
            // Seed a collection of beautiful sample products for guest users to test the Till POS system
            $products = collect([
                new Product([
                    'id' => 101,
                    'sku' => 'CONS-01',
                    'name' => 'Creative Design Consulting',
                    'description' => 'Hourly UX and UI design consultations',
                    'price' => 150.00,
                    'tax_rate' => 10.00,
                ]),
                new Product([
                    'id' => 102,
                    'sku' => 'DEV-01',
                    'name' => 'Web Development Support',
                    'description' => 'Front-end engineering services',
                    'price' => 125.00,
                    'tax_rate' => 10.00,
                ]),
                new Product([
                    'id' => 103,
                    'sku' => 'SEO-01',
                    'name' => 'SEO Marketing Setup',
                    'description' => 'Optimization package for search ranking',
                    'price' => 450.00,
                    'tax_rate' => 0.00,
                ]),
                new Product([
                    'id' => 104,
                    'sku' => 'HOST-01',
                    'name' => 'Cloud Server Hosting',
                    'description' => 'Monthly managed container hosting fee',
                    'price' => 49.00,
                    'tax_rate' => 0.00,
                ]),
            ]);
        }

        return view('invoices.till', compact('products'));
    }

    /**
     * Handle the POS Till checkout and generate the invoice.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'client_address' => ['nullable', 'string'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'client_vat' => ['nullable', 'string', 'max:50'],

            'currency' => ['required', 'string', 'max:10'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
            'theme_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{3}){1,2}$/'],
            'template_id' => ['required', 'string', 'in:blueprint,modern,classic,creative,slate'],

            'cart_items' => ['required', 'array', 'min:1'],
            'cart_items.*.name' => ['required', 'string', 'max:255'],
            'cart_items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'cart_items.*.price' => ['required', 'numeric', 'min:0'],
            'cart_items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // 1. Resolve Sender Details
        $senderName = 'POS Retail Register';
        $senderEmail = 'sales@mybusiness.com';
        $senderPhone = '';
        $senderAddress = '';
        $senderAdditional = ['tax_id' => '', 'website' => '', 'logo' => '', 'signature' => ''];

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $senderName = $user->business_name ?: $user->name;
            $senderEmail = $user->business_email ?: $user->email;
            $senderPhone = $user->business_phone ?: '';
            $senderAddress = $user->business_address ?: '';
            $senderAdditional = [
                'tax_id' => $user->business_tax_id ?: '',
                'website' => $user->business_website ?: '',
                'logo' => $user->business_logo ?: '',
                'signature' => $user->business_signature ?: '',
            ];
        }

        // 2. Format Items Payload & Compute Subtotal
        $items = [];
        $subtotal = 0;

        foreach ($request->cart_items as $cartItem) {
            $itemSub = (float) $cartItem['quantity'] * (float) $cartItem['price'];
            $subtotal += $itemSub;

            $items[] = [
                'description' => $cartItem['name'],
                'quantity' => (float) $cartItem['quantity'],
                'rate' => (float) $cartItem['price'],
                'tax_rate' => (float) ($cartItem['tax_rate'] ?? $request->tax_rate),
                'discount_rate' => 0.00,
            ];
        }

        // 3. Compute Totals
        $discountAmount = $subtotal * ((float) $request->discount_rate / 100);
        $taxable = $subtotal - $discountAmount;
        
        // Sum individual item taxes if present, or apply flat rate
        $taxAmount = 0;
        foreach ($items as $item) {
            $itemSub = $item['quantity'] * $item['rate'];
            $itemDiscount = $itemSub * ((float) $request->discount_rate / 100);
            $itemTaxable = $itemSub - $itemDiscount;
            $taxAmount += $itemTaxable * ((float) $item['tax_rate'] / 100);
        }

        $total = $taxable + $taxAmount + (float) $request->shipping_cost;

        // 4. Save Invoice
        $invoiceData = [
            'user_id' => Auth::id(),
            'invoice_number' => 'INV-POS-' . date('Ymd') . '-' . rand(100, 999),
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'sender_phone' => $senderPhone,
            'sender_address' => $senderAddress,
            'sender_additional' => $senderAdditional,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'client_address' => $request->client_address,
            'client_additional' => [
                'company' => $request->client_company ?: '',
                'vat_number' => $request->client_vat ?: '',
            ],
            'items' => $items,
            'tax_rate' => (float) $request->tax_rate,
            'discount_rate' => (float) $request->discount_rate,
            'shipping_cost' => (float) $request->shipping_cost,
            'subtotal' => round($subtotal, 2),
            'total' => round($total, 2),
            'currency' => $request->currency,
            'notes' => 'Generated instantly via POS Cash Register.',
            'terms' => 'Standard payment terms apply.',
            'template_id' => $request->template_id,
            'theme_color' => $request->theme_color,
            'status' => 'paid',
        ];

        $invoice = Invoice::create($invoiceData);

        if (! Auth::check()) {
            session()->push('guest_invoices', $invoice->id);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'POS Invoice created successfully!');
    }
}
