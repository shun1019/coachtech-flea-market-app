<?php

namespace App\Http\Controllers;

class ItemController extends Controller
{
    // 商品一覧の表示
    public function index()
    {
        return view('index');
    }

}