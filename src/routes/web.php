<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
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
Route::get('/mypage?tab=buy', [ProfileController::class, 'buyList'])->name('profile.buy');
Route::get('/mypage?tab=sell', [ProfileController::class, 'sellList'])->name('profile.sell');

// コメントの保存
Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

// 購入手続き画面
Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

// 会員登録画面
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// ログイン画面
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
