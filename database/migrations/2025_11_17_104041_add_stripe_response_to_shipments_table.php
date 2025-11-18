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
            $table->json('stripe_response')->nullable()->before('created_at');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_response');
            $table->decimal('stripe_amount_paid', 10, 2)->nullable()->after('stripe_payment_intent_id');
            $table->string('stripe_payment_status')->nullable()->after('stripe_amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['stripe_response', 'stripe_payment_intent_id', 'stripe_amount_paid', 'stripe_payment_status']);
        });
    }
};
