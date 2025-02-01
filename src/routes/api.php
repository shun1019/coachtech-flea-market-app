<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Models\Purchase;
use App\Models\Item;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Checkout\Session;

Route::post('/stripe/webhook', function (Request $request) {
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
    $payload = $request->getContent();
    $sig_header = $request->header('Stripe-Signature');

    try {
        $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    } catch (\Exception $e) {
        Log::error('Stripe Webhookエラー: ' . $e->getMessage());
        return response()->json(['error' => 'Webhook Error'], 400);
    }

    if ($event->type === 'checkout.session.async_payment_succeeded') {
        $session = $event->data->object;
        $purchase = Purchase::where('stripe_session_id', $session->id)->first();

        if ($purchase) {
            $purchase->update(['purchase_status' => 'completed']);

            $item = Item::find($purchase->item_id);
            if ($item) {
                $item->update(['status' => 'sold']);
            }
        }
    }

    return response()->json(['message' => 'Success']);
});