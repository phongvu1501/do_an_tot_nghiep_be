<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    if (!Schema::hasColumn('tables', 'status')) {
        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])
                  ->default('active')
                  ->after('limit_number');
        });
    }
}

    public function down(): void
{
    if (Schema::hasColumn('tables', 'status')) {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}};