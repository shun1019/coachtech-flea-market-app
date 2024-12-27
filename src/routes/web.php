<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ItemController::class, 'index'])->name('index');

Route::get('/?tab=mylist', [ItemController::class, 'myList'])->name('item.mylist');

Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.detail');

Route::get('/sell', [ItemController::class, 'create'])->name('sell');
Route::post('/sell', [ItemController::class, 'store'])->name('store');

Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

Route::prefix('mypage')->group(function () {
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage?tab=buy', [ProfileController::class, 'buyList'])->name('profile.buy');
    Route::get('/mypage?tab=sell', [ProfileController::class, 'sellList'])->name('profile.sell');
});

Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

Route::view('/login', 'auth.login')->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::view('/register', 'auth.register')->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');