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
        Schema::table('shipemnts', function (Blueprint $table) {
            $table->float('origin_total')->nuulable();
            $table->float('customer_total')->nuulable();
            $table->float('end_user_total')->nuulable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipemnts', function (Blueprint $table) {
            $table->dropColumn(['origin_total', 'customer_total', 'end_user_total']);
        });
    }
};
