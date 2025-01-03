<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

// ホーム画面と商品関連
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.detail');
Route::get('/?tab=mylist', [ItemController::class, 'myList'])->name('item.mylist');

// 商品出品関連
Route::get('/sell', [ItemController::class, 'create'])->name('sell');
Route::post('/sell', [ItemController::class, 'store'])->name('store');

// コメント機能
Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

// いいね機能
Route::post('/item/{item_id}/like', [ItemController::class, 'toggleLike'])->name('item.like.toggle');

// マイページ関連
Route::prefix('mypage')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/?tab=buy', [ProfileController::class, 'buyList'])->name('profile.buy');
    Route::get('/?tab=sell', [ProfileController::class, 'sellList'])->name('profile.sell');
});

// 購入関連
Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
Route::post('/purchase/{item_id}/complete', [PurchaseController::class, 'complete'])->name('purchase.complete');
Route::get('/purchase/address/{item_id}/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::post('/purchase/address/{item_id}/update', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

// 認証関連
Route::view('/login', 'auth.login')->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::view('/register', 'auth.register')->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
