<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            // Sender info
            $table->string('sender_name');
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();
            $table->text('sender_address')->nullable();
            $table->json('sender_additional')->nullable();

            // Client info
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->text('client_address')->nullable();
            $table->json('client_additional')->nullable();

            // Items and pricing
            $table->json('items');
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->decimal('discount_rate', 5, 2)->default(0.00);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('currency', 10)->default('USD');

            // Additional info
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();

            // Customization settings
            $table->string('template_id')->default('modern');
            $table->string('theme_color')->default('#3b82f6');
            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
