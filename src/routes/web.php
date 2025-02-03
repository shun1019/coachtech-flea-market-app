<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;

// 商品一覧・詳細（未認証でも閲覧可能）
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.detail');

// 出品機能（認証必須）
Route::middleware('auth')->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('sell');
    Route::post('/sell', [ItemController::class, 'store'])->name('store');
});

// コメント機能
Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

// いいね機能
Route::post('/item/{item_id}/like', [ItemController::class, 'toggleLike'])->name('item.like.toggle');

// マイページ（認証 + メール認証が必須）
Route::middleware(['auth', 'verified'])->prefix('mypage')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// 購入機能（認証必須）
Route::middleware('auth')->group(function () {
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/{item_id}/address/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/{item_id}/address/update', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/{item_id}/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');
});

// Stripe Webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

require __DIR__ . '/auth.php';
