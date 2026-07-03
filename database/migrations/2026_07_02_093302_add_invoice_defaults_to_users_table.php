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
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_currency', 10)->default('USD');
            $table->decimal('business_tax_rate', 5, 2)->default(0.00);
            $table->decimal('business_discount_rate', 5, 2)->default(0.00);
            $table->text('business_notes')->nullable();
            $table->text('business_terms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_currency',
                'business_tax_rate',
                'business_discount_rate',
                'business_notes',
                'business_terms',
            ]);
        });
    }
};
