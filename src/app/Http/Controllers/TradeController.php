<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Notifications\TradeCompletedNotification;

class TradeController extends Controller
{
    public function show(Trade $trade)
    {
        $user = Auth::user();

        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403);
        }

        // 出品者が未評価 & 購入者は評価済み → モーダル用セッションセット
        if ($trade->seller_id === $user->id) {
            $buyerRated = $trade->ratings()->where('rater_id', $trade->buyer_id)->exists();
            $sellerRated = $trade->ratings()->where('rater_id', $trade->seller_id)->exists();

            if ($buyerRated && !$sellerRated) {
                session()->flash('show_rating_modal', true);
            }
        }

        // チャットドラフト復元
        if (session()->has('chat_draft_' . $trade->id)) {
            session()->flash('chat_draft_' . $trade->id, session('chat_draft_' . $trade->id));
        }

        $otherTrades = Trade::where(function ($query) use ($user) {
            $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->where('id', '!=', $trade->id)
            ->where('status', '!=', 'completed')
            ->orderByDesc('updated_at')
            ->get();

        $chatMessages = $trade->chatMessages()->with('user.profile')->get();
        $item = $trade->item;

        return view('trades.show', compact('trade', 'chatMessages', 'item', 'otherTrades', 'user'));
    }

    public function rate(Request $request, Trade $trade)
    {
        $user = Auth::user();

        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $isBuyer = $user->id === $trade->buyer_id;
        $isSeller = $user->id === $trade->seller_id;

        if ($isSeller && !$trade->ratings()->where('rater_id', $trade->buyer_id)->exists()) {
            return redirect()->route('trade.show', $trade)->with('error', '購入者の評価完了後に評価可能です。');
        }

        Rating::updateOrCreate(
            [
                'trade_id' => $trade->id,
                'rater_id' => $user->id,
            ],
            [
                'rate' => $request->input('rating'),
            ]
        );

        $hasBuyerRating = $trade->ratings()->where('rater_id', $trade->buyer_id)->exists();
        $hasSellerRating = $trade->ratings()->where('rater_id', $trade->seller_id)->exists();

        if ($hasBuyerRating && $hasSellerRating && $trade->status !== 'completed') {
            $trade->status = 'completed';
            $trade->save();

            $otherUser = $isBuyer ? $trade->seller : $trade->buyer;
            $otherUser->notify(new TradeCompletedNotification($trade));
        }

        return redirect()->route('index')->with('success', '評価を送信しました。');
    }
}
