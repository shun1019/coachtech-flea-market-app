<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $item_id)
    {
        if (!Auth::check()) {
            abort(403, 'ログインしてください。');
        }

        $request->validate([
            'content' => 'required|max:255',
        ], [
            'content.required' => 'コメント内容を入力してください。',
            'content.max' => 'コメントは255文字以内で入力してください。',
        ]);

        Comment::create([
            'item_id' => $item_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('item.detail', ['item_id' => $item_id]);
    }
}
