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
        Schema::create('deposit_required_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique()->comment('Ngày yêu cầu đặt cọc');
            $table->text('description')->nullable()->comment('Mô tả)');
            $table->boolean('is_active')->default(true)->comment('Trạng thái kích hoạt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit_required_dates');
    }
};
