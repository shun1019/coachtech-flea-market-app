<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 商品一覧画面（トップ画面）
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/?tab=mylist', [ItemController::class, 'myList'])->name('item.mylist');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

// 商品出品画面
Route::get('/sell', [ItemController::class, 'create'])->name('sell');
Route::post('/sell', [ItemController::class, 'store'])->name('store');

// プロフィール画面
Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
