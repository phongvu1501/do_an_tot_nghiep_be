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
            $table->decimal('deposit_per_table', 10, 2)->default(300000)->after('is_active')->comment('Số tiền cọc mỗi bàn (VND)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposit_required_dates', function (Blueprint $table) {
            $table->dropColumn('deposit_per_table');
        });
    }
};
