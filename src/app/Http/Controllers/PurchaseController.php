<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入手続き画面を表示
    public function show($item_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 自分の商品や売却済の商品は購入できない
        if ($item->user_id == $user->id || $item->status === 'sold') {
            return redirect()->route('item.detail', $item_id);
        }

        $profile = $user->profile;

        // 配送先情報が未設定の場合は編集画面にリダイレクト
        if (!$profile || !$profile->zipcode || !$profile->address) {
            return redirect()->route('profile.edit');
        }

        return view('purchase', compact('item', 'profile'));
    }

    // 購入処理を実行
    public function complete(Request $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // 購入不可能な条件を確認
        if ($item->user_id == $user->id || $item->status === 'sold') {
            return redirect()->route('item.detail', $item_id);
        }

        // ユーザーのプロフィール情報を取得
        $profile = $user->profile;

        if (!$profile || !$profile->zipcode || !$profile->address) {
            return redirect()->route('purchase.show', ['item_id' => $item_id]);
        }

        // 購入履歴を保存
        Purchase::create([
            'item_id' => $item->id,
            'buyer_id' => $user->id,
            'purchase_price' => $item->price,
            'payment_method' => $request->input('payment_method'),
            'address_id' => $profile->id,
            'purchase_status' => 'completed',
        ]);

        // 商品を売却済みに更新
        $item->update(['status' => 'sold']);

        return redirect()->route('profile.index', ['tab' => 'buy']);
    }

    // 配送先情報の編集画面を表示
    public function editAddress($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if ($item->user_id == $user->id) {
            return redirect()->route('item.detail', $item_id);
        }

        $profile = $user->profile;

        return view('address_edit', compact('item_id', 'profile'));
    }

    // 配送先情報の更新処理
    public function updateAddress(Request $request, $item_id)
    {
        $user = Auth::user();

        $request->validate([
            'zipcode' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $user->profile->update([
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }
}
