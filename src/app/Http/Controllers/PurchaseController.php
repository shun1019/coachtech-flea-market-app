<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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

    // Stripe Checkout セッション作成
    public function checkout(Request $request, $item_id)
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
                // クレジットカード決済
                $session = StripeSession::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => [
                                'name' => $item->name,
                            ],
                            'unit_amount' => $item->price,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('purchase.success', ['item_id' => $item->id]),
                    'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
                ]);

                return redirect($session->url);
            } elseif
            ($paymentMethod === 'カード払い') {
                // クレジットカード決済
                $session = StripeSession::create([
                    'payment_method_types' => ['konbini'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => [
                                'name' => $item->name,
                            ],
                            'unit_amount' => $item->price,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('purchase.success', ['item_id' => $item->id]),
                    'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
                ]);

                return redirect($session->url);
            }
        } catch (\Exception $e) {
            Log::error('決済エラー: ' . $e->getMessage());
            return back()->with('error', '決済の処理に失敗しました: ' . $e->getMessage());
        }
    }

    // 購入成功画面
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
