<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入手続き画面を表示
    public function show($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if (!$this->validatePurchase($item, $user)) {
            return redirect()->route('item.detail', $item_id);
        }

        $profile = $user->profile;
        return view('purchase', compact('item', 'profile'));
    }

    // 購入処理を実行
    public function complete(PurchaseRequest $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if (!$this->validatePurchase($item, $user)) {
            return redirect()->route('item.detail', $item_id);
        }

        Purchase::create([
            'item_id' => $item->id,
            'buyer_id' => $user->id,
            'purchase_price' => $item->price,
            'payment_method' => $request->input('payment_method'),
            'address_id' => $request->input('address_id'),
            'purchase_status' => 'completed',
        ]);

        $item->update(['status' => 'sold']);

        return redirect()->route('profile.index', ['tab' => 'buy']);
    }

    // 配送先情報の編集画面を表示
    public function editAddress($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if (!$this->validatePurchase($item, $user)) {
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

        $user->profile->update($request->only(['zipcode', 'address', 'building']));

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    // 購入可能かを検証する共通メソッド
    private function validatePurchase(Item $item, $user)
    {
        return $item->user_id != $user->id && $item->status !== 'sold';
    }
}
