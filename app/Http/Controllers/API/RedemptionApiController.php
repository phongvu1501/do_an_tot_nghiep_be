<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointVoucherTier;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Controller xử lý logic Đổi điểm lấy Voucher qua API.
 * API này được gọi bởi giao diện người dùng (Frontend) và yêu cầu người dùng phải đăng nhập.
 * * Lưu ý: Lỗi xảy ra do $user là NULL khi người dùng chưa đăng nhập. 
 * Hàm get_class() không thể gọi trên giá trị NULL.
 */
class RedemptionApiController extends Controller
{
    /**
     * Lấy danh sách các cấp độ đổi điểm hợp lệ đang hoạt động.
     * GET /api/redeem/tiers
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTiers()
    {
        $tiers = PointVoucherTier::where('is_active', true)
            ->orderBy('points_required')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tiers
        ]);
    }

    /**
     * Thực hiện đổi điểm lấy voucher.
     *
     * POST /api/redeem/exchange
     * Yêu cầu: tier_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exchange(Request $request)
    {
        // 1. Validation
        $request->validate([
            'tier_id' => 'required|integer|exists:point_voucher_tiers,id',
        ], [
            'tier_id.required' => 'Missing tier ID for redemption.',
            'tier_id.exists' => 'The selected redemption tier is invalid or disabled.'
        ]);

        // 2. Lấy thông tin người dùng và cấp độ đổi điểm
        $user = Auth::user(); // User must be authenticated

        // **FIX LỖI: get_class() trên NULL**
        // Nếu người dùng chưa đăng nhập (hoặc token không hợp lệ), $user sẽ là null.
        // Cần kiểm tra $user trước khi gọi các phương thức trên nó.
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please log in to proceed with redemption.',
            ], 401);
        }

        // Lấy tier và đảm bảo nó đang active
        $tier = PointVoucherTier::where('id', $request->tier_id)->where('is_active', true)->first();

        if (!$tier) {
            return response()->json(['success' => false, 'message' => 'The redemption rule is invalid or has been disabled.'], 404);
        }

        // **Đã xóa khối dd(...) gây lỗi và đã debug xong**
        // Lấy số điểm hiện tại từ Trait HasLoyaltyPoints
        $currentPoints = $user->getCurrentPoints();

        // 3. Kiểm tra đủ điểm
        if ($currentPoints < $tier->points_required) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient points to redeem this voucher.',
                'current_points' => $currentPoints,
                'required_points' => $tier->points_required
            ], 403);
        }

        // 4. Bắt đầu Database Transaction
        try {
            DB::beginTransaction();

            $pointsToUse = $tier->points_required;
            $voucherCode = 'POINT-' . strtoupper(Str::random(8));

            // a) Ghi lại lịch sử sử dụng điểm (USE) và trừ điểm
            // Hàm usePoints() từ Trait HasLoyaltyPoints
            $user->usePoints($pointsToUse, "Redeemed voucher {$voucherCode} ({$tier->discount_percent}%) from Tier ID: {$tier->id}");

            // b) Tạo Voucher mới cho người dùng
            $voucher = Voucher::create([
                'user_id' => $user->id,
                'source' => 'points_redemption', // Nguồn gốc voucher là từ điểm thưởng
                'code' => $voucherCode,
                'discount_type' => 'percent', // Loại giảm giá là phần trăm
                'discount_value' => $tier->discount_percent, // Giá trị phần trăm
                'max_uses' => 1, // Voucher cá nhân, dùng một lần
                'min_order_value' => $tier->min_order_value, // Giá trị đơn hàng tối thiểu
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(), // Hạn sử dụng 30 ngày
                // Sử dụng order_value_allowed để lưu GIÁ TRỊ GIẢM TỐI ĐA (max_discount_value)
                'order_value_allowed' => $tier->max_discount_value,
            ]);

            DB::commit();

            // 5. Phản hồi thành công
            return response()->json([
                'success' => true,
                'message' => "Successfully redeemed voucher {$voucherCode} and deducted {$pointsToUse} points.",
                'voucher_code' => $voucher->code,
                'discount_percent' => $voucher->discount_value,
                'new_points' => $user->getCurrentPoints(),
                'max_discount' => $voucher->order_value_allowed,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the detailed error
            // \Log::error('Point redemption API error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Redemption failed due to a system error. Please try again.',
            ], 500);
        }
    }
}
