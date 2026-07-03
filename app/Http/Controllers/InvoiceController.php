<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $invoices = Invoice::where('user_id', Auth::id())->latest()->get();

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        $defaultInvoice = [
            'invoice_number' => 'INV-'.date('Ymd').'-'.rand(100, 999),
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),

            // Default values
            'sender_name' => 'My Business Ltd',
            'sender_email' => 'hello@mybusiness.com',
            'sender_phone' => '',
            'sender_address' => '',
            'sender_additional' => [
                'tax_id' => '',
                'website' => '',
                'logo' => '',
                'signature' => '',
            ],

            'client_name' => 'John Doe',
            'client_email' => 'john.doe@example.com',
            'client_phone' => '+1 (555) 014-3847',
            'client_address' => "456 Customer Ave\nApartment 4B\nGotham, NY 10002",
            'client_additional' => ['company' => 'Gotham Enterprises', 'vat_number' => 'VAT-987654321'],
            'items' => [
                ['description' => 'Web Design & Development Service', 'quantity' => 1, 'rate' => 1200.00, 'tax_rate' => 10, 'discount_rate' => 0],
                ['description' => 'Monthly Server Maintenance & Support', 'quantity' => 2, 'rate' => 150.00, 'tax_rate' => 10, 'discount_rate' => 5],
            ],
            'tax_rate' => 0,
            'discount_rate' => 0,
            'shipping_cost' => 0,
            'currency' => 'USD',
            'notes' => 'Thank you for your business. It is a pleasure working with you!',
            'terms' => "Payment is due within 30 days of the invoice date.\nPlease send payments to our bank account or via client portal.",
            'template_id' => 'modern',
            'theme_color' => '#3b82f6',
            'status' => 'draft',
        ];

        // Override default items/values with Sandbox query parameters if present
        if (request()->has('qty')) {
            $defaultInvoice['items'][0]['quantity'] = (int) request()->query('qty');
        }
        if (request()->has('rate')) {
            $defaultInvoice['items'][0]['rate'] = (float) request()->query('rate');
        }
        if (request()->has('tax')) {
            $defaultInvoice['tax_rate'] = (float) request()->query('tax');
            $defaultInvoice['items'][0]['tax_rate'] = (float) request()->query('tax');
        }
        if (request()->has('discount')) {
            $defaultInvoice['discount_rate'] = (float) request()->query('discount');
            $defaultInvoice['items'][0]['discount_rate'] = (float) request()->query('discount');
        }
        if (request()->has('currency')) {
            $defaultInvoice['currency'] = request()->query('currency');
        }
        if (request()->has('shipping')) {
            $defaultInvoice['shipping_cost'] = (float) request()->query('shipping');
        }

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $defaultInvoice['sender_name'] = $user->business_name ?: $user->name;
            $defaultInvoice['sender_email'] = $user->business_email ?: $user->email;
            $defaultInvoice['sender_phone'] = $user->business_phone ?: '';
            $defaultInvoice['sender_address'] = $user->business_address ?: '';
            $defaultInvoice['sender_additional'] = [
                'tax_id' => $user->business_tax_id ?: '',
                'website' => $user->business_website ?: '',
                'logo' => $user->business_logo ?: '',
                'signature' => $user->business_signature ?: '',
            ];
            $defaultInvoice['tax_rate'] = $user->business_tax_rate ?: 0;
            $defaultInvoice['discount_rate'] = $user->business_discount_rate ?: 0;
            $defaultInvoice['currency'] = $user->business_currency ?: 'USD';
            if ($user->business_notes !== null) {
                $defaultInvoice['notes'] = $user->business_notes;
            }
            if ($user->business_terms !== null) {
                $defaultInvoice['terms'] = $user->business_terms;
            }
        }

        return view('invoices.create', compact('defaultInvoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $invoice = Invoice::create($data);

        if (! Auth::check()) {
            session()->push('guest_invoices', $invoice->id);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): View
    {
        // Viewable if it is public or belongs to user
        if ($invoice->user_id !== null && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $defaultInvoice = $invoice->toArray();
        $defaultInvoice['invoice_date'] = $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '';
        $defaultInvoice['due_date'] = $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '';

        return view('invoices.create', [
            'invoice' => $invoice,
            'defaultInvoice' => $defaultInvoice,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->update($request->validated());

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }
}
