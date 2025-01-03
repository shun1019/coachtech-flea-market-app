<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入手続き画面を表示
    public function show($item_id, Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', '購入手続きにはログインが必要です。');
        }

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->user_id == $user->id || $item->status === 'sold') {
            return redirect()->route('item.detail', $item_id)->with('error', '購入できません。');
        }

        $profile = $user->profile;

        if (!$profile || !$profile->zipcode || !$profile->address) {
            return redirect()->route('profile.edit')->with('error', '配送先情報が未設定です。');
        }

        return view('purchase', compact('item', 'profile'));
    }

    // 購入処理を実行
    public function complete(Request $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if ($item->user_id == $user->id || $item->status === 'sold') {
            return redirect()->route('item.detail', $item_id)->with('error', '購入できません。');
        }

        $item->update([
            'status' => 'sold',
            'buyer_id' => $user->id,
        ]);

        return redirect()->route('profile.index', ['tab' => 'buy'])->with('success', '購入が完了しました。');
    }

    // 配送先情報の編集画面を表示
    public function editAddress($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if ($item->user_id == $user->id) {
            return redirect()->route('item.detail', $item_id)->with('error', '自分の商品は購入できません。');
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
        ], [
            'zipcode.required' => '郵便番号を入力してください。',
            'zipcode.regex' => '郵便番号は「XXX-XXXX」の形式で入力してください。',
            'address.required' => '住所を入力してください。',
        ]);

        $user->profile->update([
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('success', '配送先情報が更新されました。');
    }
}
