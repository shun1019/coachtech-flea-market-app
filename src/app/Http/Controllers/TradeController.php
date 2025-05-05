<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    /**
     * 取引画面の表示
     */
    public function show(Trade $trade)
    {
        $user = Auth::user();

        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403);
        }

        if (session()->has('chat_draft_' . $trade->id)) {
            session()->flash('chat_draft_' . $trade->id, session('chat_draft_' . $trade->id));
        }

        $otherTrades = Trade::where(function ($query) use ($user) {
            $query->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
        })
            ->where('id', '!=', $trade->id)
            ->where('status', '!=', 'completed')
            ->orderByDesc('updated_at')
            ->get();

        $chatMessages = $trade->chatMessages()->with('user.profile')->get();
        $item = $trade->item;

        return view('trades.show', compact('trade', 'chatMessages', 'item', 'otherTrades', 'user'));
    }

    /**
     * 取引の完了処理
     */
    public function complete(Trade $trade)
    {
        $user = Auth::user();

        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403);
        }

        if (request()->has('body')) {
            session()->flash('chat_draft_' . $trade->id, request('body'));
        }

        $rating = request('rating');
        if ($rating !== null) {
            $trade->rating = $rating;
        }

        $trade->status = 'completed';
        $trade->save();

        return redirect()
            ->route('index')
            ->with('success', '取引が完了しました');
    }
}
