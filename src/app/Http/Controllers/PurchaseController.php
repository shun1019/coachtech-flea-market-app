<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

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

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentMethod = $request->input('payment_method');

            if ($paymentMethod === 'カード払い') {
                $session = StripeSession::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => ['name' => $item->name],
                            'unit_amount' => $item->price,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('purchase.success', ['item_id' => $item->id]),
                    'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
                ]);

                return redirect($session->url);
            } elseif ($paymentMethod === 'コンビニ払い'
            ) {
                $session = StripeSession::create([
                    'payment_method_types' => ['konbini'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => ['name' => $item->name],
                            'unit_amount' => $item->price,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('purchase.success', ['item_id' => $item->id]),
                    'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
                    'metadata' => [
                        'item_id' => $item->id,
                        'buyer_id' => $user->id,
                    ],
                ]);

                return redirect($session->url);
            } else {
                return back()->withErrors(['payment_method' => '無効な支払い方法が選択されました。']);
            }
        } catch (\Exception $e) {
            Log::error('決済エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => '決済の処理に失敗しました: ' . $e->getMessage()]);
        }
    }

    public function success($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        Purchase::create([
            'item_id' => $item->id,
            'buyer_id' => $user->id,
            'purchase_price' => $item->price,
            'payment_method' => 'Stripe Checkout',
            'address_id' => $user->profile->id,
            'purchase_status' => 'completed',
        ]);

        $item->update(['status' => 'sold']);

        return redirect()->route('profile.index')->with('success', '購入が完了しました！');
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
            'building' => ['required', 'string',],
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
