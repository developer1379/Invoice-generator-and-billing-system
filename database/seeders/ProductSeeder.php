<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        // Set default dynamic fields settings for the user
        $settings = $user->settings ?? [];
        $settings['product_custom_fields'] = [
            ['name' => 'Color', 'type' => 'text', 'required' => false, 'tip' => 'Primary color tag'],
            ['name' => 'Size', 'type' => 'text', 'required' => false, 'tip' => 'Item size, e.g. M, L, XL'],
            ['name' => 'Warranty', 'type' => 'number', 'required' => false, 'tip' => 'Warranty duration in months'],
        ];
        $user->update(['settings' => $settings]);

        $products = [
            [
                'sku' => 'CONS-DEV',
                'name' => 'Creative Consulting Fee',
                'description' => 'Professional strategy consulting and architecture reviews.',
                'price' => 150.00,
                'tax_rate' => 15.00,
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&auto=format&fit=crop',
                'custom_fields' => ['Color' => 'Blue', 'Size' => 'N/A', 'Warranty' => 0],
            ],
            [
                'sku' => 'SAAS-ENT',
                'name' => 'Enterprise SaaS Subscription',
                'description' => 'Access to the global analytics suite, multi-tenant workspace, and premium SLAs.',
                'price' => 999.00,
                'tax_rate' => 18.00,
                'image_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&auto=format&fit=crop',
                'custom_fields' => ['Color' => 'Indigo', 'Size' => 'Premium', 'Warranty' => 12],
            ],
            [
                'sku' => 'SRV-HOST',
                'name' => 'Dedicated Server Hosting',
                'description' => 'High-performance cloud virtualization server with 99.9% uptime SLA.',
                'price' => 45.00,
                'tax_rate' => 0.00,
                'image_url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=400&auto=format&fit=crop',
                'custom_fields' => ['Color' => 'Grey', 'Size' => 'Rack-2U', 'Warranty' => 36],
            ],
            [
                'sku' => 'DSN-PROT',
                'name' => 'UI/UX Prototyping Kit',
                'description' => 'Complete wireframe design kit and vector asset bundle for Figma and Sketch.',
                'price' => 120.00,
                'tax_rate' => 10.00,
                'image_url' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=400&auto=format&fit=crop',
                'custom_fields' => ['Color' => 'Multi', 'Size' => 'Digital', 'Warranty' => 0],
            ],
            [
                'sku' => 'PKG-DELV',
                'name' => 'Global Shipping & Delivery',
                'description' => 'Expedited priority logistics courier shipping service across all continents.',
                'price' => 25.00,
                'tax_rate' => 5.00,
                'image_url' => 'https://images.unsplash.com/photo-1553413077-190dd305871c?w=400&auto=format&fit=crop',
                'custom_fields' => ['Color' => 'Yellow', 'Size' => 'Standard', 'Warranty' => 0],
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['user_id' => $user->id, 'sku' => $p['sku']],
                $p
            );
        }
    }
}
