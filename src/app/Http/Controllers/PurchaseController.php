<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Trade;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
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

    public function checkout(PurchaseRequest $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        if (!$this->validatePurchase($item, $user)) {
            return redirect()->route('item.detail', $item_id);
        }

        try {
            $trade = Trade::create([
                'item_id' => $item->id,
                'buyer_id' => $user->id,
                'seller_id' => $item->user_id,
            ]);

            Purchase::create([
                'item_id' => $item->id,
                'buyer_id' => $user->id,
                'purchase_price' => $item->price,
                'payment_method' => '銀行振込',
                'address_id' => $user->profile->id,
                'purchase_status' => 'pending',
                'trade_id' => $trade->id,
            ]);

            $item->update(['status' => 'sold']);

            return redirect()->route('trade.show', ['trade' => $trade->id]);
        } catch (\Exception $e) {
            Log::error('購入処理エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => '購入処理に失敗しました: ' . $e->getMessage()]);
        }
    }

    public function processing($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('purchase_processing', compact('item'));
    }

    public function success($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        $purchase = Purchase::where('item_id', $item->id)
            ->where('buyer_id', $user->id)
            ->where('purchase_status', 'completed')
            ->firstOrFail();

        $item->update(['status' => 'sold']);

        if ($purchase->trade_id) {
            return redirect()->route('mypage.trades.show', ['trade' => $purchase->trade_id])
                ->with('success', '購入が完了しました！取引チャット画面に移動します。');
        } else {
            return redirect()->route('profile.index')->with('success', '購入が完了しましたが、取引チャットが見つかりませんでした。');
        }
    }

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

    public function updateAddress(Request $request, $item_id)
    {
        $user = Auth::user();

        $request->validate([
            'zipcode' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string'],
            'building' => ['required', 'string'],
        ], [
            'zipcode.required' => '郵便番号を入力してください。',
            'zipcode.regex' => '郵便番号は「XXX-XXXX」の形式で入力してください。',
            'address.required' => '住所を入力してください。',
            'building.required' => '建物名を入力してください。',
        ]);

        $user->profile->update($request->only(['zipcode', 'address', 'building']));

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    public function cancel($item_id)
    {
        return redirect()->route('purchase.show', ['item_id' => $item_id])
            ->with('error', '購入がキャンセルされました。');
    }

    private function validatePurchase(Item $item, $user)
    {
        return $item->user_id != $user->id && $item->status !== 'sold';
    }
}
