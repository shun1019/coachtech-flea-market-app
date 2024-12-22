<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|max:50',
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名は必須です',
            'price.required' => '価格は必須です',
            'description.required' => '商品の説明は必須です',
            'category_id.required' => 'カテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'image.required' => '商品の画像をアップロードしてください',
        ];
    }
}
