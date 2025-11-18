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
            $table->string('carrier_delivery_days')->nullable()->after('label_id');
            $table->string('estimated_delivery_date')->nullable()->after('carrier_delivery_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('carrier_delivery_days');
            $table->dropColumn('estimated_delivery_date');
        });
    }
};
