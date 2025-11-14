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
        Schema::create('point_voucher_tiers', function (Blueprint $table) {
            $table->id();
            $table->integer('points_required')->unique();
            $table->decimal('discount_percent', 5, 2);
            $table->decimal('max_discount_value', 10, 2)->nullable();
            $table->decimal('min_order_value', 10, 2)->nullable();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_voucher_tiers');
    }
};
