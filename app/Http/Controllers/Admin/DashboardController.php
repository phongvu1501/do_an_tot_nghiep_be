<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboard = "Trang thống kê";

        return view('admin.layouts.dashboard',compact('dashboard'));
    }
}
