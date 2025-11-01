<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['table_number', 'capacity', 'available_date', 'available_from', 'available_until']);
            
            $table->string('name')->after('id');
            $table->integer('limit_number')->default(8)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['name', 'limit_number']);
            
            $table->string('table_number');
            $table->integer('capacity');
            $table->date('available_date')->nullable();
            $table->time('available_from')->nullable();
            $table->time('available_until')->nullable();
        });
    }
};
