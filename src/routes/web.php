<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

// 会員登録画面
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// ログイン画面
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// 商品一覧画面（トップ画面）
Route::get('/', [ItemController::class, 'index'])->name('index');
