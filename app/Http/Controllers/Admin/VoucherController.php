<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointVoucherTier;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $title = "Trang voucher";

        $vouchers = Voucher::with(['tier', 'users'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        $totalUsers = User::count();

        return view('admin.vouchers.voucher.index', compact('title', 'vouchers', 'totalUsers'));
    }

    public function show(Voucher $voucher)
    {
        $voucher->load(['tier', 'users']);
        $title = "Chi tiết voucher";
        
        $totalUsers = User::count();

        return view('admin.vouchers.voucher.show', compact('title', 'voucher', 'totalUsers'));
    }

    public function create()
    {
        $title = "Thêm voucher mới";
        $tiers = PointVoucherTier::where('is_active', true)->get();
        $users = User::orderByRaw('CASE WHEN phone IS NULL OR phone = "" THEN 1 ELSE 0 END')
                     ->orderBy('name', 'asc')
                     ->get();

        return view('admin.vouchers.voucher.create', compact('title', 'tiers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tier_id' => 'nullable|exists:point_voucher_tiers,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => function ($attribute, $value, $fail) {
                if (!is_numeric($value) || !User::where('id', $value)->exists()) {
                    $fail('Người dùng không tồn tại.');
                }
            },
            'code' => 'nullable|string|unique:vouchers,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Giá trị giảm phần trăm không được lớn hơn 100%.');
                    }
                },
            ],
            'order_value_allowed' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'min_order_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if (empty($request->code)) {
            do {
                $code = strtoupper('VC-' . uniqid());
            } while (Voucher::where('code', $code)->exists());
        } else {
            $code = strtoupper(trim($request->code));
        }

        $voucher = Voucher::create([
            'tier_id' => $request->tier_id,
            'code' => $code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'order_value_allowed' => $request->order_value_allowed,
            'max_uses' => $request->max_uses,
            'min_order_value' => $request->min_order_value,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        $userIds = $request->user_ids;
        if (count($userIds) === User::count()) {
            $allUserIds = User::pluck('id')->toArray();
            $voucher->users()->sync($allUserIds);
        } else {
            $userIds = array_filter($userIds, function($id) {
                return is_numeric($id);
            });
            $voucher->users()->sync($userIds);
        }

        return redirect()->route('admin.vouchers.voucher.index')->with('success', 'Voucher được tạo thành công.');
    }

    public function edit(Voucher $voucher)
    {
        $voucher->load(['tier', 'users']);
        $tiers = PointVoucherTier::where('is_active', true)->get();
        $users = User::orderByRaw('CASE WHEN phone IS NULL OR phone = "" THEN 1 ELSE 0 END')
                     ->orderBy('name', 'asc')
                     ->get();
        $title = 'Chỉnh sửa Voucher';
        
        $totalUsers = User::count();
        $assignedUsersCount = $voucher->users ? $voucher->users->count() : 0;
        $isForAllUsers = $assignedUsersCount > 0 && $assignedUsersCount == $totalUsers;
        
        $selectedUserIds = old('user_ids', $voucher->users->pluck('id')->toArray());

        return view('admin.vouchers.voucher.edit', compact('voucher', 'title', 'tiers', 'users', 'totalUsers', 'isForAllUsers', 'selectedUserIds'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'tier_id' => 'nullable|exists:point_voucher_tiers,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => function ($attribute, $value, $fail) {
                if (!is_numeric($value) || !User::where('id', $value)->exists()) {
                    $fail('Người dùng không tồn tại.');
                }
            },
            'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Giá trị giảm phần trăm không được lớn hơn 100%.');
                    }
                },
            ],
            'order_value_allowed' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'min_order_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $voucher->update([
            'tier_id' => $request->tier_id,
            'code' => $request->code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'order_value_allowed' => $request->order_value_allowed,
            'max_uses' => $request->max_uses,
            'min_order_value' => $request->min_order_value,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        $userIds = $request->user_ids;
        if (count($userIds) === User::count()) {
            $allUserIds = User::pluck('id')->toArray();
            $voucher->users()->sync($allUserIds);
        } else {
            $userIds = array_filter($userIds, function($id) {
                return is_numeric($id);
            });
            $voucher->users()->sync($userIds);
        }

        return redirect()->route('admin.vouchers.voucher.index')->with('success', 'Cập nhật voucher thành công.');
    }

    public function disable(Request $request, Voucher $voucher)
    {
        // Nếu có status trong request thì dùng, không thì toggle
        if ($request->has('status')) {
            $status = $request->input('status');
        } else {
            // Toggle: nếu đang active thì inactive, ngược lại thì active
            $status = $voucher->status === 'active' ? 'inactive' : 'active';
        }
        
        $voucher->status = $status;
        $voucher->save();

        $message = $status === 'active' 
            ? 'Đã kích hoạt voucher ' . $voucher->code 
            : 'Đã tạm dừng voucher ' . $voucher->code;

        return redirect()->route('admin.vouchers.voucher.index')
            ->with('success', $message);
    }

    public function destroy(Voucher $voucher)
    {
        try {
            $voucherCode = $voucher->code;
            // Đổi status thành inactive thay vì xóa
            $voucher->status = 'inactive';
            $voucher->save();

            return redirect()->route('admin.vouchers.voucher.index')
                ->with('success', 'Đã tạm dừng voucher ' . $voucherCode . ' thành công.');
        } catch (\Exception $e) {
            return redirect()->route('admin.vouchers.voucher.index')
                ->with('error', 'Có lỗi xảy ra khi tạm dừng voucher: ' . $e->getMessage());
        }
    }
}
