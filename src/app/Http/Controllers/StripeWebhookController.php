<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Purchase;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // checkout.session.completed イベントをチェック
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $itemId = $session->metadata->item_id;
            $buyerId = $session->metadata->buyer_id;

            $item = Item::find($itemId);
            if ($item && $item->status !== 'sold') {
                Purchase::create([
                    'item_id' => $item->id,
                    'buyer_id' => $buyerId,
                    'purchase_price' => $item->price,
                    'payment_method' => 'Stripe Checkout (konbini)',
                    'purchase_status' => 'completed',
                ]);
                $item->update(['status' => 'sold']);
            }
        }

        return response('Webhook handled', 200);
    }
}
