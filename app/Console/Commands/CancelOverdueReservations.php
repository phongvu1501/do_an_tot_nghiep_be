<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelOverdueReservations extends Command
{
    protected $signature = 'reservations:cancel-overdue';

    protected $description = 'Tự động hủy các đơn đặt bàn đã qua thời gian ca mà chưa được chuyển sang đang phục vụ';

    public function handle()
    {
        $shiftEndTimes = [
            'morning' => '10:00',   
            'afternoon' => '14:00', 
            'evening' => '18:00',   
            'night' => '22:00',    
        ];

        // Lấy các đơn đã đặt cọc  chưa  chuyển sang th đang phục vụ
        $reservations = Reservation::where('status', 'deposit_paid')
            ->whereIn('shift', array_keys($shiftEndTimes))
            ->get();

        $cancelledCount = 0;

        foreach ($reservations as $reservation) {
            $shiftEnd = $shiftEndTimes[$reservation->shift] ?? null;

            if (!$shiftEnd) {
                continue;
            }

            // Tạo thời gian kết thúc ca
            $shiftEndTime = Carbon::parse($reservation->reservation_date)
                ->setTimeFromTimeString($shiftEnd);

            
            if (Carbon::now()->greaterThan($shiftEndTime)) {
                $reservation->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Khách không đến'
                ]);

                $cancelledCount++;
            }
        }

        if ($cancelledCount > 0) {
            $this->info("Đã hủy {$cancelledCount} đơn đặt bàn do khách không đến.");
        } else {
            $this->info('Không có đơn đặt bàn nào cần hủy.');
        }

        return 0;
    }
}
