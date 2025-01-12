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
            $items = Item::with('categories')->paginate(8);
        } else {
            if ($tab === 'recommended') {
                $items = Item::with('categories')
                    ->where('user_id', '!=', $user->id)
                    ->paginate(8)
                    ->withQueryString();
            } elseif ($tab === 'mylist') {
                $likedItemIds = $user->likes()->pluck('item_id');
                $items = Item::with('categories')
                    ->where('user_id', '!=', $user->id)
                    ->whereIn('id', $likedItemIds)
                    ->paginate(8)
                    ->withQueryString();
            } else {
                $items = Item::with('categories')->paginate(8);
            }
        }

        return view('index', compact('items', 'tab'));
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
            return redirect()->back();
        }

        $liked = $user->likes()->toggle($item_id);
        $item->increment('like_count', count($liked['attached']));
        $item->decrement('like_count', count($liked['detached']));

        return redirect()->back();
    }
}
