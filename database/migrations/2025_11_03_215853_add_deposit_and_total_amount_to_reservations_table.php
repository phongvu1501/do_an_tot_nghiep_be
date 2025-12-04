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
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'deposit')) {
                $table->decimal('deposit', 10, 2)->default(0)->after('user_id');
            }

            if (!Schema::hasColumn('reservations', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('deposit');
            }
        });
    }

    public function down(): void
    {
        $columnsToDrop = [];

        if (Schema::hasColumn('reservations', 'deposit')) {
            $columnsToDrop[] = 'deposit';
        }

        if (Schema::hasColumn('reservations', 'total_amount')) {
            $columnsToDrop[] = 'total_amount';
        }

        if (!empty($columnsToDrop)) {
            Schema::table('reservations', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};
