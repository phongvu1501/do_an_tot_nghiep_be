<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointVoucherTier;
use Illuminate\Http\Request;

class TierController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Danh sách tiêu chí quy đổi điểm lấy voucher';

        $query = PointVoucherTier::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('points_required')) {
            $query->where('points_required', '>=', $request->points_required);
        }

        if ($request->filled('discount_percent')) {
            $query->where('discount_percent', '>=', $request->discount_percent);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }

        $tiers = $query
            ->orderBy('id', 'desc')
            ->paginate(10) 
            ->appends($request->query()); 


        return view('admin.tiers.index', compact('title', 'tiers'));
    }


    public function show(PointVoucherTier $tier)
    {
        $title = 'Chi tiết tiêu chí quy đổi điểm lấy voucher';
        return view('admin.tiers.show', compact('title', 'tier'));
    }

    public function create()
    {
        $title = 'Thêm mới tiêu chí';
        return view('admin.tiers.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:point_voucher_tiers,name',
            'points_required' => 'required|integer|min:1',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'max_discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',
            'order_value_allowed' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        PointVoucherTier::create($request->all());

        return redirect()->route('admin.tiers.index')
            ->with('success', 'Tiêu chí được tạo thành công.');
    }

    public function edit(PointVoucherTier $tier)
    {
        $title = 'Chỉnh sửa tiêu chí';
        return view('admin.tiers.edit', compact('title', 'tier'));
    }

    public function update(Request $request, PointVoucherTier $tier)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:point_voucher_tiers,name,' . $tier->id,
            'points_required' => 'required|integer|min:1',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'max_discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',
            'order_value_allowed' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $tier->update($request->all());

        return redirect()->route('admin.tiers.index')
            ->with('success', 'Cập nhật tiêu chí thành công.');
    }

    public function disable(PointVoucherTier $tier)
    {
        $tier->is_active = false;
        $tier->save();

        return redirect()->route('admin.tiers.index')
            ->with('success', 'Đã tạm dừng tiêu chí ' . $tier->name);
    }

    public function enable(PointVoucherTier $tier)
    {
        $tier->is_active = true;
        $tier->save();

        return redirect()->route('admin.tiers.index')
            ->with('success', 'Đã kích hoạt tiêu chí ' . $tier->name);
    }
}
