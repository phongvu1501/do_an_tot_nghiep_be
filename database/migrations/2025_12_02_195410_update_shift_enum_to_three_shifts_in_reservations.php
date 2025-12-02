<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chuyển đổi dữ liệu: night -> evening (merge night vào evening)
        DB::statement("UPDATE reservations SET shift = 'evening' WHERE shift = 'night'");
        
        // Cập nhật enum từ 4 ca sang 3 ca
        DB::statement("ALTER TABLE reservations MODIFY COLUMN shift ENUM('morning', 'afternoon', 'evening') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục enum về 4 ca
        DB::statement("ALTER TABLE reservations MODIFY COLUMN shift ENUM('morning', 'afternoon', 'evening', 'night') NOT NULL");
        
        // Lưu ý: Không thể tự động khôi phục dữ liệu từ evening về night vì không biết đâu là night cũ
    }
};
