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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('tax_id')->nullable();
            $table->integer('years_in_business')->nullable();
            $table->enum('business_type', ['retail', 'wholesale'])->default('retail');
            $table->longText('notes')->nullable();

            // Calculations
            $table->decimal('margin')->default(0.00)->comment('Margin percentage for shipping quotes');
            $table->decimal('customer_margin')->default(0.00);
            $table->decimal('tax_percentage')->default(0.00)->comment("Tax percentage that applies if ther's an packaging amount");

            // Stripe related fields
            $table->string('stripe_customer_id')->nullable();

            // Carriers data
            $table->string('carrier_id')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->after('id')->index();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->after('id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
};
