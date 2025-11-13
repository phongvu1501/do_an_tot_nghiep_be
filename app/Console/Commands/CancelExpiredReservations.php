<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredReservations extends Command
{
  
    protected $signature = 'reservations:cancel-expired';

   
    protected $description = 'Tự động hủy các đơn đặt bàn chờ đặt cọc quá 15 phút';

    public function handle()
    {
        $expirationTime = Carbon::now()->subMinutes(15);

        Reservation::whereIn('status', ['deposit_pending', 'pending'])
            ->where('created_at', '<=', $expirationTime)
            ->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Hệ thống tự động hủy do khách hàng không đặt cọc trong thời gian quy định (15 phút)'
            ]);

        return 0;
    }
}
