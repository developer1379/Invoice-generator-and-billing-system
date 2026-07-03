<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $items = [
            [
                'description' => fake()->words(3, true),
                'quantity' => fake()->numberBetween(1, 5),
                'rate' => fake()->randomFloat(2, 50, 300),
                'tax_rate' => fake()->randomElement([0, 5, 10, 15]),
                'discount_rate' => fake()->randomElement([0, 5, 10]),
            ],
            [
                'description' => fake()->words(4, true),
                'quantity' => fake()->numberBetween(1, 3),
                'rate' => fake()->randomFloat(2, 20, 150),
                'tax_rate' => fake()->randomElement([0, 5, 10]),
                'discount_rate' => 0,
            ],
        ];

        // Compute subtotal and total
        $subtotal = 0;
        $total = 0;
        foreach ($items as $item) {
            $itemSubtotal = $item['quantity'] * $item['rate'];
            $discount = $itemSubtotal * ($item['discount_rate'] / 100);
            $taxableAmount = $itemSubtotal - $discount;
            $tax = $taxableAmount * ($item['tax_rate'] / 100);

            $subtotal += $itemSubtotal;
            $total += ($taxableAmount + $tax);
        }

        // Apply a small global discount or tax occasionally
        $taxRate = fake()->randomElement([0, 5, 12, 18]);
        $discountRate = fake()->randomElement([0, 5, 10]);
        $shippingCost = fake()->randomElement([0, 15, 25]);

        // Calculate final total
        $globalDiscount = $total * ($discountRate / 100);
        $globalTax = ($total - $globalDiscount) * ($taxRate / 100);
        $finalTotal = $total - $globalDiscount + $globalTax + $shippingCost;

        return [
            'uuid' => (string) Str::uuid(),
            'invoice_number' => 'INV-'.fake()->unique()->numberBetween(10000, 99999),
            'invoice_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'due_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),

            'sender_name' => fake()->company(),
            'sender_email' => fake()->companyEmail(),
            'sender_phone' => fake()->phoneNumber(),
            'sender_address' => fake()->address(),
            'sender_additional' => [
                'tax_id' => 'VAT-'.fake()->numberBetween(100000, 999999),
                'website' => fake()->domainName(),
            ],

            'client_name' => fake()->name(),
            'client_email' => fake()->safeEmail(),
            'client_phone' => fake()->phoneNumber(),
            'client_address' => fake()->address(),
            'client_additional' => [
                'company' => fake()->company(),
                'vat_number' => 'VAT-'.fake()->numberBetween(100000, 999999),
            ],

            'items' => $items,
            'tax_rate' => $taxRate,
            'discount_rate' => $discountRate,
            'shipping_cost' => $shippingCost,
            'subtotal' => round($subtotal, 2),
            'total' => round($finalTotal, 2),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'INR']),

            'notes' => fake()->sentence(10),
            'terms' => 'Please pay within 30 days of invoice date.',
            'template_id' => fake()->randomElement(['modern', 'classic', 'creative', 'slate']),
            'theme_color' => fake()->randomElement(['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#6366f1']),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'overdue']),
        ];
    }
}
