<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('discount_type', ['percent', 'amount'])->after('code');
            $table->decimal('discount_value', 12, 2)->after('discount_type');
            $table->decimal('min_order_value', 12, 2)->nullable()->after('discount_value');
            $table->decimal('order_value_allowed', 12, 2)->nullable()->after('min_order_value');
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'min_order_value',
                'order_value_allowed',
            ]);
        });
    }
};
