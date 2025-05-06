<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;

// 商品一覧・詳細
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.detail');

// 認証後のみ利用可能なルート群
Route::middleware('auth')->group(function () {

    // 出品
    Route::get('/sell', [ItemController::class, 'create'])->name('sell');
    Route::post('/sell', [ItemController::class, 'store'])->name('store');

    // 取引画面
    Route::get('/trade/{trade}', [TradeController::class, 'show'])->name('trade.show');

    // 評価（双方向対応）
    Route::post('/trade/{trade}/rate', [TradeController::class, 'rate'])->name('trade.rate');

    // チャット
    Route::post('/trade/{trade}/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::put('/chat/{message}', [ChatController::class, 'update'])->name('chat.update');
    Route::delete('/chat/{message}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::post('/trade/{trade}/chat/draft', [ChatController::class, 'saveDraft'])->name('chat.draft');

    // 購入処理
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/{item_id}/address/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/{item_id}/address/update', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

// コメント・いいね
Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');
Route::post('/item/{item_id}/like', [ItemController::class, 'toggleLike'])->name('item.like.toggle');

// マイページ（認証・メール認証）
Route::middleware(['auth', 'verified'])->prefix('mypage')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('mypage.trades.show');
});

require __DIR__ . '/auth.php';
