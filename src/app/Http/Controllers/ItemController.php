<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $tab = request()->query('tab', 'recommended');
        $user = Auth::user();

        if (!$user) {
            $items = collect();
        } else {
            if ($tab === 'mylist') {
                $likedItemIds = $user->likes()->pluck('item_id')->toArray();
                $items = Item::with('categories')
                ->whereIn('id', $likedItemIds)
                    ->where('user_id', '!=', $user->id)
                    ->get();
            } elseif ($tab === 'recommended') {
                $items = Item::with('categories')
                ->where('user_id', '!=', $user->id)
                    ->where('status', 'available')
                    ->get();
            } else {
                $items = Item::with('categories')->paginate(8);
            }
        }

        return view('index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('sell', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'condition' => $request->condition,
            'image' => $path,
            'status' => 'available',
            'like_count' => 0,
            'comments_count' => 0,
        ]);

        if ($request->categories) {
            $item->categories()->attach($request->categories);
        }

        return redirect()->route('profile.index');
    }

    public function show($item_id)
    {
        $item = Item::with('categories')->findOrFail($item_id);
        $comments = $item->comments()->with('user')->get();
        $user = Auth::user();

        $userLiked = $user && $user->likes()->where('item_id', $item_id)->exists();

        return view('detail', compact('item', 'comments', 'user', 'userLiked'));
    }

    public function toggleLike($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'ログインが必要です。');
        }

        if ($user->likes()->where('item_id', $item_id)->exists()) {
            $user->likes()->detach($item_id);
            $item->decrement('like_count');
        } else {
            $user->likes()->attach($item_id);
            $item->increment('like_count');
        }

        return redirect()->back();
    }
}
