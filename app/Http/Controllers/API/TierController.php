<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PointVoucherTier;
use Illuminate\Http\Request;

class TierController extends Controller
{
    public function getAllTiers()
    {
        $tiers = PointVoucherTier::where('is_active', 1)
            ->orderBy('points_required', 'asc')
            ->get();
        if ($tiers->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Không có tiêu chí nào hoạt động'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => $tiers
        ]);
    }
}
