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
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('customer_margin')->comment("Tax percentage that applies if ther's an packaging amount");
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('tax_percentage');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tax_percentage');
        });
    }
};
