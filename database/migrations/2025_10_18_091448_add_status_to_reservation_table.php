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
            $table->enum('status', ['pending', 'confirmed', 'serving', 'completed', 'cancelled', 'suspended', 'waiting_for_payment'])
                  ->default('pending')
                  ->comment('Trạng thái của đơn đặt bàn: "pending" - Chờ xác nhận, "confirmed" - Đã xác nhận, "serving" - Đang phục vụ, "waiting_for_payment" - Đang chờ thanh toán, "completed" - Hoàn tất, "cancelled" - Hủy, "suspended" - Tạm dừng');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
