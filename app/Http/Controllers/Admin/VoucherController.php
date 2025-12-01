<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointVoucherTier;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $title = "Trang voucher";
        $vouchers = Voucher::with(['tier', 'user'])->get(); // load relation để show
        return view('admin.vouchers.voucher.index', compact('title', 'vouchers'));
    }

    public function show(Voucher $voucher)
    {
        $voucher->load(['tier', 'user']);
        $title = "Chi tiết voucher";
        return view('admin.vouchers.voucher.show', compact('title', 'voucher'));
    }

    public function create()
    {
        $title = "Thêm voucher mới";
        $tiers = PointVoucherTier::where('is_active', true)->get();
        $users = User::all();
        return view('admin.vouchers.voucher.create', compact('title', 'tiers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tier_id' => 'nullable|exists:point_voucher_tiers,id',
            'user_id' => 'nullable|exists:users,id',
            'code' => 'required|string|unique:vouchers,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required', 'numeric', 'min:0',
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

        $voucher = Voucher::create([
            'tier_id' => $request->tier_id,
            'user_id' => $request->user_id,
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

        return redirect()->route('admin.vouchers.voucher.index')->with('success', 'Voucher được tạo thành công.');
    }

    public function edit(Voucher $voucher)
    {
        $voucher->load(['tier', 'user']);
        $tiers = PointVoucherTier::where('is_active', true)->get();
        $users = User::all();
        $title = 'Chỉnh sửa Voucher';
        return view('admin.vouchers.voucher.edit', compact('voucher', 'title', 'tiers', 'users'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'tier_id' => 'nullable|exists:point_voucher_tiers,id',
            'user_id' => 'nullable|exists:users,id',
            'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required', 'numeric', 'min:0',
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
            'user_id' => $request->user_id,
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

        return redirect()->route('admin.vouchers.voucher.index')->with('success', 'Cập nhật voucher thành công.');
    }

    public function disable(Voucher $voucher)
    {
        $voucher->status = 'inactive';
        $voucher->save();

        return redirect()->route('admin.vouchers.voucher.index')
            ->with('success', 'Đã tạm dừng voucher ' . $voucher->code);
    }
}
