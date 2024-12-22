<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
class ItemController extends Controller
{
    // 商品一覧の表示
    public function index()
    {
        return view('index');
    }

    // 商品出品フォームの表示
    public function create()
    {
        $categories = Category::all();
        return view('sell', compact('categories'));
    }

    // 商品データの保存
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

        // マイページへリダイレクト
        return redirect()->route('profile.index');
    }
}