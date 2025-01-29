<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Stripe Webhook Received:', $payload);

        if ($payload['type'] === 'payment_intent.succeeded') {
            // Stripeからの支払い成功通知を処理
            $paymentIntent = $payload['data']['object'];

            // 購入データを更新
            $purchase = Purchase::where('stripe_payment_id', $paymentIntent['id'])->first();
            if ($purchase) {
                $purchase->update(['purchase_status' => 'completed']);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
