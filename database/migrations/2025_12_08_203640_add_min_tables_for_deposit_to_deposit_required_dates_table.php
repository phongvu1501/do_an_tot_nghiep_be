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
            $table->integer('min_tables_for_deposit')->default(2)->after('deposit_vip_rooms')->comment('Số bàn tối thiểu cần cọc (ngày thường)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_required_dates', function (Blueprint $table) {
            $table->dropColumn('min_tables_for_deposit');
        });
    }
};
