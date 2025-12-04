<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DepositRequiredDate;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepositRequiredDateController extends Controller
{
  
    public function index(Request $request)
    {
        $dates = DepositRequiredDate::where('is_active', true)
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $dates->map(function ($date) {
                return [
                    'id' => $date->id,
                    'date' => $date->date->format('Y-m-d'),
                    'description' => $date->description,
                ];
            }),
        ], 200);
    }

  
    public function check(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $holidayDate = DepositRequiredDate::where('is_active', true)
            ->whereDate('date', $request->date)
            ->orderBy('created_at', 'desc')
            ->first();

        $requiresDeposit = $holidayDate !== null;
        $depositAmount = null;

        if ($holidayDate) {
            // Lấy số tiền cọc từ ngày lễ: ưu tiên deposit_normal_tables, nếu không có thì dùng deposit_per_table, cuối cùng mới dùng settings
            $depositAmount = $holidayDate->deposit_normal_tables 
                ?? $holidayDate->deposit_per_table 
                ?? \App\Models\Setting::getValue('deposit_normal_tables', 500000);
        }

        return response()->json([
            'success' => true,
            'requires_deposit' => $requiresDeposit,
            'deposit_amount' => $depositAmount,
            'date' => $request->date,
        ], 200);
    }
}
