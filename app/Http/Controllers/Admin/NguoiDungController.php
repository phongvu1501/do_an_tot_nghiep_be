<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class NguoiDungController extends Controller
{
    public function index(){
        $title = "Trang tài khoản người dùng";

        $accountUsers = User::where('role','user')->get();

        return view('admin.taiKhoan.nguoiDung.index',compact('title','accountUsers'));
    }

    public function show($id){

    }
}
