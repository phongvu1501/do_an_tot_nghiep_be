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
        Schema::table('point_voucher_tiers', function (Blueprint $table) {
            $table->decimal('order_value_allowed', 10, 2)
                ->nullable()
                ->after('min_order_value')
                ->comment('Giá trị đơn hàng áp dụng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_voucher_tiers', function (Blueprint $table) {
            $table->dropColumn('order_value_allowed');
        });
    }
};
