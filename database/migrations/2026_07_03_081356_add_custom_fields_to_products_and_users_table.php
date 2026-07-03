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
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('tax_rate');
            $table->json('custom_fields')->nullable()->after('image_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('business_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'custom_fields']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
};
