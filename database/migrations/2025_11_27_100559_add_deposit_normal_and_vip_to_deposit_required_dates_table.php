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
        Schema::table('deposit_required_dates', function (Blueprint $table) {
            $table->decimal('deposit_normal_tables', 10, 2)->nullable()->after('deposit_per_table')->comment('Tiền cọc cho toàn bộ bàn thường');
            $table->decimal('deposit_vip_rooms', 10, 2)->nullable()->after('deposit_normal_tables')->comment('Tiền cọc cho toàn bộ phòng VIP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_required_dates', function (Blueprint $table) {
            $table->dropColumn(['deposit_normal_tables', 'deposit_vip_rooms']);
        });
    }
};
