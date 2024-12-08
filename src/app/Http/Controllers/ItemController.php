<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|max:255',
            'category' => 'required|exists:categories,id',
            'condition' => 'required|max:50',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => '商品名は必須です。',
            'price.required' => '価格は必須です。',
            'description.required' => '商品の説明は必須です。',
            'category.required' => 'カテゴリーを選択してください。',
            'condition.required' => '商品の状態を選択してください。',
            'image.required' => '商品画像をアップロードしてください。',
        ]);
    }
}