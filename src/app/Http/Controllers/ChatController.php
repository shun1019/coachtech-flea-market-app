<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Trade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ChatMessageRequest;

class ChatController extends Controller
{
    /**
     * チャットメッセージ保存
     */
    public function store(ChatMessageRequest $request, Trade $trade)
    {
        $user = Auth::user();

        $message = new ChatMessage();
        $message->user_id = $user->id;
        $message->trade_id = $trade->id;
        $message->body = $request->input('body');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
            $message->image_path = $imagePath;
        }

        $message->save();

        // 下書きセッションは削除（送信後は不要になるため）
        Session::forget("draft_message_{$trade->id}");

        return redirect()->route('trade.show', ['trade' => $trade->id]);
    }

    /**
     * 編集内容保存
     */
    public function update(ChatMessageRequest $request, ChatMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $message->body = $request->input('body');

        if ($request->hasFile('image')) {
            if ($message->image_path && Storage::disk('public')->exists($message->image_path)) {
                Storage::disk('public')->delete($message->image_path);
            }

            $imagePath = $request->file('image')->store('chat_images', 'public');
            $message->image_path = $imagePath;
        }

        $message->save();

        return redirect()->route('trade.show', ['trade' => $message->trade_id])
            ->with('success', 'メッセージを更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy(ChatMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        if ($message->image_path && Storage::disk('public')->exists($message->image_path)) {
            Storage::disk('public')->delete($message->image_path);
        }

        $tradeId = $message->trade_id;
        $message->delete();

        return redirect()->route('trade.show', ['trade' => $tradeId])
            ->with('success', 'メッセージを削除しました。');
    }

    /**
     * チャット本文の下書きをセッションに保存
     */
    public function saveDraft(Trade $trade)
    {
        $body = request('body');
        Session::put("draft_message_{$trade->id}", $body);

        return response()->json(['status' => 'ok']);
    }
}
