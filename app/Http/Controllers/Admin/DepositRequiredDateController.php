<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositRequiredDate;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DepositRequiredDateController extends Controller
{
    /**
     * Hiển thị danh sách ngày cần cọc
     */
    public function index(Request $request)
    {
        $query = DepositRequiredDate::orderBy('date', 'desc');

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Lọc theo ngày
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $allDates = $query->orderBy('date', 'asc')->get();
        
        // Nhóm các ngày liên tiếp thành range
        $groupedDates = $this->groupConsecutiveDates($allDates);
        
        // Paginate manual cho grouped dates
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = $groupedDates->slice($offset, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $groupedDates->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Lấy giá trị từ database (settings table), nếu không có thì lấy mặc định
        $depositNormalTables = Setting::getValue('deposit_normal_tables', 500000);
        $depositVipRooms = Setting::getValue('deposit_vip_rooms', 1000000);

        return view('admin.depositRequiredDate.index', [
            'title' => 'Quản lý ngày yêu cầu đặt cọc',
            'dates' => $paginator,
            'deposit_normal_tables' => $depositNormalTables,
            'deposit_vip_rooms' => $depositVipRooms,
        ]);
    }

    /**
     * Hiển thị form tạo ngày cần cọc
     */
    public function create()
    {
        return view('admin.depositRequiredDate.create', [
            'title' => 'Thêm ngày yêu cầu đặt cọc',
        ]);
    }

    /**
     * Lưu ngày cần cọc mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today|unique:deposit_required_dates,date',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'deposit_per_table' => 'nullable|numeric|min:0',
            'deposit_normal_tables' => 'nullable|numeric|min:1',
            'deposit_vip_rooms' => 'nullable|numeric|min:1',
        ], [
            'date.required' => 'Vui lòng chọn ngày!',
            'date.date' => 'Ngày không hợp lệ!',
            'date.after_or_equal' => 'Ngày phải từ hôm nay trở đi!',
            'date.unique' => 'Ngày này đã được thêm vào danh sách!',
            'description.max' => 'Mô tả không được quá 500 ký tự!',
            'deposit_per_table.numeric' => 'Số tiền cọc phải là số!',
            'deposit_per_table.min' => 'Số tiền cọc phải lớn hơn hoặc bằng 0!',
            'deposit_normal_tables.numeric' => 'Tiền cọc bàn thường phải là số!',
            'deposit_normal_tables.min' => 'Tiền cọc bàn thường phải lớn hơn 0!',
            'deposit_vip_rooms.numeric' => 'Tiền cọc phòng VIP phải là số!',
            'deposit_vip_rooms.min' => 'Tiền cọc phòng VIP phải lớn hơn 0!',
        ]);

        $depositNormalTables = $validated['deposit_normal_tables'] ?? session('deposit_normal_tables', 500000);
        $depositVipRooms = $validated['deposit_vip_rooms'] ?? session('deposit_vip_rooms', 1000000);

        DepositRequiredDate::create([
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'deposit_per_table' => $validated['deposit_per_table'] ?? 300000,
            'deposit_normal_tables' => $depositNormalTables,
            'deposit_vip_rooms' => $depositVipRooms,
        ]);

        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', 'Thêm ngày yêu cầu đặt cọc thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa ngày cần cọc
     */
    public function edit($id)
    {
        $depositDate = DepositRequiredDate::findOrFail($id);
        return view('admin.depositRequiredDate.edit', [
            'title' => 'Chỉnh sửa ngày yêu cầu đặt cọc',
            'date' => $depositDate,
        ]);
    }

    /**
     * Cập nhật ngày cần cọc
     */
    public function update(Request $request, $id)
    {
        $depositDate = DepositRequiredDate::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today|unique:deposit_required_dates,date,' . $id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'deposit_per_table' => 'required|numeric|min:0',
        ], [
            'date.required' => 'Vui lòng chọn ngày!',
            'date.date' => 'Ngày không hợp lệ!',
            'date.after_or_equal' => 'Ngày phải từ hôm nay trở đi!',
            'date.unique' => 'Ngày này đã được thêm vào danh sách!',
            'description.max' => 'Mô tả không được quá 500 ký tự!',
            'deposit_per_table.required' => 'Vui lòng nhập số tiền cọc!',
            'deposit_per_table.numeric' => 'Số tiền cọc phải là số!',
            'deposit_per_table.min' => 'Số tiền cọc phải lớn hơn hoặc bằng 0!',
        ]);

        $depositDate->update([
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'deposit_per_table' => $validated['deposit_per_table'],
        ]);

        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', 'Cập nhật ngày yêu cầu đặt cọc thành công!');
    }

   
    public function updateRange(Request $request)
    {
        $validated = $request->validate([
            'date_ids' => 'required|array|min:1',
            'date_ids.*' => 'exists:deposit_required_dates,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'deposit_per_table' => 'required|numeric|min:0',
        ], [
            'deposit_per_table.required' => 'Vui lòng nhập số tiền cọc!',
            'deposit_per_table.numeric' => 'Số tiền cọc phải là số!',
            'deposit_per_table.min' => 'Số tiền cọc phải lớn hơn hoặc bằng 0!',
        ]);

        $dateIds = $validated['date_ids'];
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $description = $validated['description'] ?? null;
        $isActive = $validated['is_active'] ?? true;
        $depositPerTable = $validated['deposit_per_table'];

        DepositRequiredDate::whereIn('id', $dateIds)->delete();

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            DepositRequiredDate::create([
                'date' => $currentDate->toDateString(),
                'description' => $description,
                'is_active' => $isActive,
                'deposit_per_table' => $depositPerTable,
            ]);
            $currentDate->addDay();
        }

        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', 'Cập nhật range ngày thành công!');
    }

 
    public function destroyRange(Request $request)
    {
        $validated = $request->validate([
            'date_ids' => 'required|array|min:1',
            'date_ids.*' => 'exists:deposit_required_dates,id',
        ]);

        $count = DepositRequiredDate::whereIn('id', $validated['date_ids'])->delete();

        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', "Đã xóa {$count} ngày thành công!");
    }

 
    public function destroy($id)
    {
        $depositDate = DepositRequiredDate::findOrFail($id);
        $depositDate->delete();

        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', 'Xóa ngày yêu cầu đặt cọc thành công!');
    }

 
    public function toggleStatus(Request $request, $id)
    {
        $depositDate = DepositRequiredDate::findOrFail($id);
        $newStatus = !$depositDate->is_active;
        
        if ($request->has('date_ids') && is_array($request->date_ids)) {
            $updated = DepositRequiredDate::whereIn('id', $request->date_ids)
                ->update(['is_active' => $newStatus]);
            $status = $newStatus ? 'kích hoạt' : 'vô hiệu hóa';
            return redirect()->route('admin.depositRequiredDate.index')
                ->with('success', "Đã {$status} {$updated} ngày!");
        }
        
        $depositDate->is_active = $newStatus;
        $depositDate->save();

        $status = $depositDate->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', "Đã {$status} ngày yêu cầu đặt cọc!");
    }

   
    public function storeRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'deposit_per_table' => 'required|numeric|min:0',
        ], [
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu!',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc!',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau ngày bắt đầu!',
            'deposit_per_table.required' => 'Vui lòng nhập số tiền cọc!',
            'deposit_per_table.numeric' => 'Số tiền cọc phải là số!',
            'deposit_per_table.min' => 'Số tiền cọc phải lớn hơn hoặc bằng 0!',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $added = 0;
        $skipped = 0;

        while ($startDate->lte($endDate)) {
            $exists = DepositRequiredDate::whereDate('date', $startDate->toDateString())->exists();
            
            if (!$exists) {
                DepositRequiredDate::create([
                    'date' => $startDate->toDateString(),
                    'description' => $validated['description'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                    'deposit_per_table' => $validated['deposit_per_table'],
                ]);
                $added++;
            } else {
                $skipped++;
            }
            
            $startDate->addDay();
        }

        $message = "Đã thêm {$added} ngày";
        if ($skipped > 0) {
            $message .= " (bỏ qua {$skipped} ngày đã tồn tại)";
        }

        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', $message . '!');
    }

   
    private function groupConsecutiveDates($dates)
    {
        if ($dates->isEmpty()) {
            return collect();
        }

        $sortedDates = $dates->sortBy('date');
        $groups = collect();
        $currentGroup = null;

        foreach ($sortedDates as $date) {
            $dateCarbon = Carbon::parse($date->date)->startOfDay();

            if ($currentGroup === null) {
                $currentGroup = [
                    'start_date' => $dateCarbon,
                    'end_date' => $dateCarbon,
                    'dates' => collect([$date]),
                    'description' => $date->description,
                    'is_active' => $date->is_active,
                    'deposit_per_table' => $date->deposit_per_table,
                    'is_range' => false,
                ];
            } else {
                $lastDate = Carbon::parse($currentGroup['end_date'])->startOfDay();
                $daysDiff = $lastDate->diffInDays($dateCarbon);

                if ($daysDiff == 1 && 
                    $currentGroup['description'] === $date->description &&
                    $currentGroup['is_active'] === $date->is_active &&
                    $currentGroup['deposit_per_table'] == $date->deposit_per_table) {
                    $currentGroup['end_date'] = $dateCarbon;
                    $currentGroup['dates']->push($date);
                    $currentGroup['is_range'] = true;
                } else {
                    $groups->push($currentGroup);
                    $currentGroup = [
                        'start_date' => $dateCarbon,
                        'end_date' => $dateCarbon,
                        'dates' => collect([$date]),
                        'description' => $date->description,
                        'is_active' => $date->is_active,
                        'deposit_per_table' => $date->deposit_per_table,
                        'is_range' => false,
                    ];
                }
            }
        }

        if ($currentGroup !== null) {
            $groups->push($currentGroup);
        }

        return $groups;
    }

    public function updateDepositSettings(Request $request)
    {
        $validated = $request->validate([
            'deposit_normal_tables' => 'required|numeric|min:1',
            'deposit_vip_rooms' => 'required|numeric|min:1',
        ], [
            'deposit_normal_tables.required' => 'Vui lòng nhập tiền cọc cho bàn thường!',
            'deposit_normal_tables.numeric' => 'Tiền cọc bàn thường phải là số!',
            'deposit_normal_tables.min' => 'Tiền cọc bàn thường phải lớn hơn 0!',
            'deposit_vip_rooms.required' => 'Vui lòng nhập tiền cọc cho phòng VIP!',
            'deposit_vip_rooms.numeric' => 'Tiền cọc phòng VIP phải là số!',
            'deposit_vip_rooms.min' => 'Tiền cọc phòng VIP phải lớn hơn 0!',
        ]);

        // Lưu vào database (settings table) để không bị mất khi xóa session hoặc clone code mới
        Setting::setValue('deposit_normal_tables', $validated['deposit_normal_tables']);
        Setting::setValue('deposit_vip_rooms', $validated['deposit_vip_rooms']);

        return redirect()->route('admin.depositRequiredDate.index')
            ->with('success', 'Đã cập nhật cấu hình tiền cọc thành công!');
    }
}
