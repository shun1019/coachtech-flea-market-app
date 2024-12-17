<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// 商品一覧画面（トップ画面）
Route::get('/', [ItemController::class, 'index'])->name('index');

// マイリスト表示（トップ画面のタブ管理）
Route::get('/?tab=mylist', [ItemController::class, 'myList'])->name('item.mylist');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'profile'])->name('item.profile');

// 商品出品
Route::get('/sell', [ItemController::class, 'create'])->name('sell');
Route::post('/sell', [ItemController::class, 'store'])->name('store');

// プロフィール画面
Route::prefix('mypage')->group(function () {
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage?tab=buy', [ProfileController::class, 'buyList'])->name('profile.buy');
    Route::get('/mypage?tab=sell', [ProfileController::class, 'sellList'])->name('profile.sell');
});

Route::view('/login', 'auth.login')->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::view('/register', 'auth.register')->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');