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
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('packaging_amount', 10, 2)->nullable()->after('stripe_payment_status');
            $table->decimal('total_with_packaging', 10, 2)->nullable()->after('packaging_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['packaging_amount', 'total_with_packaging']);
        });
    }
};
