<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DepositRequiredDate;
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

        $requiresDeposit = DepositRequiredDate::where('is_active', true)
            ->whereDate('date', $request->date)
            ->exists();

        return response()->json([
            'success' => true,
            'requires_deposit' => $requiresDeposit,
            'date' => $request->date,
        ], 200);
    }
}
