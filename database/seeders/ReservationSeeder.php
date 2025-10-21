<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reservations')->insert([
            [
                'order_code' => 'RSV' . Str::random(7),
                'user_id' => 1, 
                'reservation_date' => '2025-10-25',
                'reservation_time' => '19:00:00',
                'num_people' => 4,
                'depsection' => 'Đặt bàn khu vực ngoài trời',
                'voucher_id' => null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_code' => 'RSV' . Str::random(7),
                'user_id' => 1, 
                'reservation_date' => '2025-10-26',
                'reservation_time' => '11:30:00',
                'num_people' => 2,
                'depsection' => 'Đã đặt bàn số 10',
                'voucher_id' => null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}