<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['payment_token', 'payment_expires_at']);
            DB::statement("
                ALTER TABLE `reservations`
                CHANGE COLUMN `status` `status` ENUM('pending', 'deposit_pending', 'deposit_paid', 'serving', 'completed', 'cancelled')
                DEFAULT 'deposit_pending'
                COMMENT 'Trạng thái của đơn đặt bàn: \"pending\" - Chờ xác nhận, \"deposit_pending\" - Chờ đặt cọc, \"deposit_paid\" - Đã đặt cọc, \"serving\" - Đang phục vụ, \"completed\" - Hoàn tất, \"cancelled\" - Hủy'
            ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('payment_token')->nullable();
            $table->timestamp('payment_expires_at')->nullable();

            DB::statement("
                ALTER TABLE `reservations`
                CHANGE COLUMN `status` `status` ENUM('pending', 'confirmed', 'serving', 'completed', 'cancelled', 'suspended', 'waiting_for_payment')
                DEFAULT 'pending'
                COMMENT 'Trạng thái của đơn đặt bàn: \"pending\" - Chờ xác nhận, \"confirmed\" - Đã xác nhận, \"serving\" - Đang phục vụ, \"waiting_for_payment\" - Đang chờ thanh toán, \"completed\" - Hoàn tất, \"cancelled\" - Hủy, \"suspended\" - Tạm dừng'
            ");
        });
    }
};
