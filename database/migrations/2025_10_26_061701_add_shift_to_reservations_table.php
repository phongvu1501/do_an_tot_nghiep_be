<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('reservation_time');
            
            $table->enum('shift', ['morning', 'afternoon', 'evening'])->after('reservation_date');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('shift');
            $table->time('reservation_time')->after('reservation_date');
        });
    }
};
