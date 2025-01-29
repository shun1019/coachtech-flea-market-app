<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (!User::where('email', $value)->exists()) {
                        $fail('ログイン情報が登録されていません');
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $this->email)->first();

                    if ($user && !Hash::check($value, $user->password)) {
                        $fail('パスワードが正しくありません。');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
        ];
    }
}
