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
        Schema::table('vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('vouchers', 'discount_type')) {
                $table->dropColumn('discount_type');
            }

            if (Schema::hasColumn('vouchers', 'discount_value')) {
                $table->dropColumn('discount_value');
            }

            if (Schema::hasColumn('vouchers', 'min_order_value')) {
                $table->dropColumn('min_order_value');
            }

            if (Schema::hasColumn('vouchers', 'order_value_allowed')) {
                $table->dropColumn('order_value_allowed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('min_order_value', 15, 2)->nullable();
            $table->decimal('order_value_allowed', 15, 2)->nullable();
        });
    }
};
