<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Purchase;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response('Webhook error', 400);
        }

        Log::info('Received Stripe webhook:', ['event' => $event]);

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $metadata = $paymentIntent->metadata;
            $itemId = $metadata->item_id ?? null;
            $buyerId = $metadata->buyer_id ?? null;

            if ($itemId && $buyerId) {
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
        }

        return response('Webhook handled', 200);
    }
}
