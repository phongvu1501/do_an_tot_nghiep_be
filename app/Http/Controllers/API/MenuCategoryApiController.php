<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;

class MenuCategoryApiController extends Controller
{
    // API: Lấy danh sách danh mục món ăn
    public function index()
    {
        $categories = MenuCategory::all(['id', 'name', 'description', 'created_at', 'updated_at']);

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách danh mục thành công',
            'data' => $categories
        ], 200);
    }
}