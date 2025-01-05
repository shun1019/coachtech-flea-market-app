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
            'categories' => 'required',
            'condition' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名は必須です',
            'name.max' => '商品名は255文字以内で入力してください',
            'price.required' => '価格は必須です',
            'price.numeric' => '価格は数値で入力してください',
            'price.min' => '価格は0以上で入力してください',
            'description.required' => '商品の説明は必須です',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'categories.required' => 'カテゴリーを1つ以上選択してください',
            'condition.required' => '商品の状態を選択してください',
            'image.required' => '商品の画像をアップロードしてください',
            'image.image' => 'アップロードするファイルは画像形式である必要があります',
            'image.mimes' => '画像ファイルはjpeg, png, jpg形式のみ対応しています',
        ];
    }
}
