<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Purchase;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Stripe Webhook Received!');
        $payload = $request->all();
        Log::info('Webhook Payload:', $payload);

        if ($payload['type'] === 'payment_intent.succeeded' || $payload['type'] === 'charge.succeeded') {
            $paymentObject = $payload['data']['object'];
            $purchase = Purchase::where('stripe_payment_id', $paymentObject['id'])->first();

            if ($purchase) {
                $purchase->update(['purchase_status' => 'completed']);
                Log::info("Purchase status updated to completed for ID: {$purchase->id}");
            } else {
                Log::warning("Purchase not found for Stripe Payment ID: {$paymentObject['id']}");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
