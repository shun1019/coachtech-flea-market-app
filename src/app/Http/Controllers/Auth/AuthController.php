<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle the login process.
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません。'],
            ]);
        }

        return redirect()->route('index')->with('success', 'ログインに成功しました。');
    }

    /**
     * Handle the registration process.
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // ユーザーを登録
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 登録後に自動ログイン
        Auth::login($user);

        return redirect()->route('profile.edit')->with('success', '登録が完了しました！プロフィールを編集してください。');
    }

    /**
     * Handle the logout process.
     */
    public function logout()
    {
        Auth::logout();

        return redirect()->route('login.form')->with('success', 'ログアウトしました。');
    }
}
