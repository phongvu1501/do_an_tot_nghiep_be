<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $title = "Trang voucher";
        $vouchers = Voucher::all();
        return view('admin.vouchers.voucher.index', compact('title', 'vouchers'));
    }

    public function show(Voucher $voucher)
    {
        $title = "Chi tiết voucher";
        return view('admin.vouchers.voucher.show', compact('title', 'voucher'));
    }

    public function create()
    {
        $title = "Thêm voucher mới";
        return view('admin.vouchers.voucher.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code',
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
        ], [
            'code.unique' => 'Mã voucher đã tồn tại.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        $request->merge([
            'start_date' => date('Y-m-d', strtotime($request->start_date)),
            'end_date' => date('Y-m-d', strtotime($request->end_date)),
        ]);

        Voucher::create([
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

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        $title = 'Chỉnh sửa Voucher';
        return view('admin.vouchers.voucher.edit', compact('voucher', 'title'));
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $request->validate([
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
        try {
            $voucher->status = 'inactive';
            $voucher->save();

            return redirect()->route('admin.vouchers.voucher.index')
                ->with('success', 'Đã chuyển voucher ' . $voucher->code . ' sang trạng thái Tạm dừng.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Không thể chuyển trạng thái voucher. Vui lòng thử lại.');
        }
    }
}
