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
        return view('index');
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

        return view('detail', compact('item', 'comments', 'user'));
    }
}