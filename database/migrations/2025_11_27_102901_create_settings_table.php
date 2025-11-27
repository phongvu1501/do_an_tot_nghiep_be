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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Tên key cấu hình');
            $table->text('value')->nullable()->comment('Giá trị cấu hình');
            $table->timestamps();
        });

        // Thêm dữ liệu mặc định
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'deposit_normal_tables', 'value' => '500000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'deposit_vip_rooms', 'value' => '1000000', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
