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
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('deposit', 10, 2)->nullable()->after('status')->comment('Số tiền đặt cọc');
            $table->decimal('total_amount', 10, 2)->nullable()->after('deposit')->comment('Tổng số tiền');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['deposit', 'total_amount']);
        });
    }
};
