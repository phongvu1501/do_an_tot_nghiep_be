<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BanAnController extends Controller{
    public function index(){
        $banAn = "Trang bàn ăn";
        return view('admin.banAn.banAn',compact('banAn'));
    }
}