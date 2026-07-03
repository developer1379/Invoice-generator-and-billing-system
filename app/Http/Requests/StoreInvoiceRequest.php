<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow anyone to create invoices (public/guest usage is supported)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_number' => ['required', 'string', 'max:50'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],

            // Sender validation
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_email' => ['nullable', 'email', 'max:255'],
            'sender_phone' => ['nullable', 'string', 'max:50'],
            'sender_address' => ['nullable', 'string'],
            'sender_additional' => ['nullable', 'array'],
            'sender_additional.tax_id' => ['nullable', 'string', 'max:50'],
            'sender_additional.website' => ['nullable', 'string', 'max:255'],
            'sender_additional.logo' => ['nullable', 'string'],
            'sender_additional.signature' => ['nullable', 'string'],
            'sender_additional.logo_width' => ['nullable', 'integer'],
            'sender_additional.logo_height' => ['nullable', 'integer'],
            'sender_additional.logo_x' => ['nullable', 'integer'],
            'sender_additional.logo_y' => ['nullable', 'integer'],

            // Client validation
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'client_address' => ['nullable', 'string'],
            'client_additional' => ['nullable', 'array'],
            'client_additional.company' => ['nullable', 'string', 'max:255'],
            'client_additional.vat_number' => ['nullable', 'string', 'max:50'],

            // Items validation
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Summary validation
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],

            // Notes & terms
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],

            // Styling
            'template_id' => ['required', 'string', 'in:blueprint,modern,classic,creative,slate'],
            'theme_color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{3}){1,2}$/'],
            'status' => ['required', 'string', 'in:draft,sent,paid,overdue'],
        ];
    }
}
