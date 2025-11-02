<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuApiController extends Controller
{
    /**
     * Lấy danh sách món ăn.
     * - Nếu ?category_id= có giá trị thì lọc theo category_id đó.
     * - Trả về thông tin menu + category_id + category_name.
     * - Chỉ lấy menu chưa xóa mềm (deleted_at IS NULL).
     * - Thêm image_url đầy đủ (storage).
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $query = DB::table('menus')
            ->join('menu_categories', 'menus.category_id', '=', 'menu_categories.id')
            ->select(
                'menus.id',
                'menus.name',
                'menus.description',
                'menus.price',
                'menus.image',
                'menus.status',
                'menus.created_at',
                'menu_categories.id as category_id',
                'menu_categories.name as category_name'
            )
            // Loại bỏ các hàng đã xóa mềm
            ->whereNull('menus.deleted_at');

        // Nếu FE truyền category_id thì lọc
        if (!is_null($categoryId) && $categoryId !== '') {
            $query->where('menus.category_id', $categoryId);
        }

        // (Tuỳ chọn) chỉ lấy menu active nếu FE muốn: bỏ comment dòng dưới
        // $query->where('menus.status', 1);

        $menus = $query->get();

        // Thêm trường image_url đầy đủ (nếu có)
        $menus = $menus->map(function ($m) {
            $m = (array) $m;
            $m['image_url'] = $m['image']
                ? url('storage/' . ltrim($m['image'], '/'))
                : null;
            // chuyển số trạng thái thành boolean/int rõ ràng nếu cần
            $m['status'] = (int) $m['status'];
            return $m;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách món ăn thành công',
            'data' => $menus,
        ], 200);
    }
}
