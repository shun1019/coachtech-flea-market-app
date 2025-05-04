<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    /**
     * 認可ロジック
     */
    public function authorize()
    {
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules()
    {
        return [
            'body' => 'required|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * バリデーションエラーメッセージ
     */
    public function messages()
    {
        return [
            'body.required' => 'メッセージを入力してください。',
            'body.string'   => 'メッセージは文字列で入力してください。',
            'body.max'      => 'メッセージは400文字以内で入力してください。',
            'image.image'   => '添付ファイルは画像である必要があります。',
            'image.mimes'   => '画像はjpeg, png, jpg, gif形式のみ対応しています。',
            'image.max'     => '画像サイズは2MB以下にしてください。',
        ];
    }
}
