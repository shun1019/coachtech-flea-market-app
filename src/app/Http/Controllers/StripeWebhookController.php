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
        // 受け取ったペイロード（リクエストの中身）を取得
        $payload = $request->getContent();
        // Stripe が送る署名ヘッダー
        $sig_header = $request->header('Stripe-Signature');
        // .env ファイルに設定した Webhook シークレット
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            // Stripe のイベントオブジェクトに変換（署名の検証も行う）
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            // 無効なペイロードの場合
            Log::error('Invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // 署名の検証に失敗した場合
            Log::error('Invalid signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // checkout.session.completed イベントをチェック
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            // セッションに設定した metadata から item_id と buyer_id を取得
            $itemId = $session->metadata->item_id;
            $buyerId = $session->metadata->buyer_id;

            // アイテムを取得し、未購入なら処理実行
            $item = Item::find($itemId);
            if ($item && $item->status !== 'sold') {
                // 購入情報をデータベースに記録する
                Purchase::create([
                    'item_id' => $item->id,
                    'buyer_id' => $buyerId,
                    'purchase_price' => $item->price,
                    'payment_method' => 'Stripe Checkout (konbini)',
                    'purchase_status' => 'completed',
                ]);
                // アイテムの状態を更新
                $item->update(['status' => 'sold']);
            }
        }

        return response('Webhook handled', 200);
    }
}
