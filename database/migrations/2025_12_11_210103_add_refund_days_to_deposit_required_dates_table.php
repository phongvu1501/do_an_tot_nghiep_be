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
            $table->integer('refund_days')->nullable()->default(1)->after('is_active')->comment('Số ngày trước ngày đặt để được hoàn tiền khi hủy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_required_dates', function (Blueprint $table) {
            $table->dropColumn('refund_days');
        });
    }
};
