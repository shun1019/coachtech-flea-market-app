<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', '購入手続きにはログインが必要です。');
        }

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $profile = $user->profile;

        return view('purchase', compact('item', 'profile'));
    }

    public function editAddress($item_id)
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('address_edit', compact('profile', 'item_id'));
    }

    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'zipcode' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ], [
            'zipcode.required' => '郵便番号を入力してください。',
            'zipcode.regex' => '郵便番号は「XXX-XXXX」の形式で入力してください。',
            'address.required' => '住所を入力してください。',
            'building.string' => '建物名は文字列で入力してください。',
        ]);

        $profile = Auth::user()->profile;

        $profile->update([
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('success', '配送先を更新しました。');
    }
}
