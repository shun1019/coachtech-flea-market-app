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

        if ($tab === 'recommended') {
            if ($user) {
                $likedItemIds = session('liked_items', []);
                $items = Item::whereIn('id', $likedItemIds)->get();
            } else {
                $items = collect();
            }
        } elseif ($tab === 'mylist') {
            $items = $user ? $user->items : collect();
        } else {
            $items = Item::paginate(8);
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
            'category_id' => $request->category_id,
            'condition' => $request->condition,
            'image' => $path,
            'status' => 'available',
            'like_count' => 0,
            'comments_count' => 0,
        ]);

        return redirect()->route('profile.index');
    }

    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $comments = $item->comments()->with('user')->get();
        $user = Auth::user();

        $likedItems = session('liked_items', []);
        $userLiked = in_array($item->id, $likedItems);

        return view('detail', compact('item', 'comments', 'user', 'userLiked'));
    }

    public function toggleLike($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'ログインが必要です。');
        }

        $likedItems = session('liked_items', []);

        if (in_array($item->id, $likedItems)) {
            $item->decrement('like_count');
            $likedItems = array_diff($likedItems, [$item->id]);
        } else {
            $item->increment('like_count');
            $likedItems[] = $item->id;
        }

        session(['liked_items' => $likedItems]);

        return redirect()->back();
    }
}
