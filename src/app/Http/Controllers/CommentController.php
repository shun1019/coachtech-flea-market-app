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
        $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'コメント内容は必須です。',
        ]);

        $item = Item::findOrFail($item_id);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
            'content' => $request->content,
        ]);

        return redirect()->route('item.show', ['item_id' => $item_id]);
    }
}
