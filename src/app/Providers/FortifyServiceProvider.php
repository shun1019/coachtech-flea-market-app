<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Fortifyのログイン画面
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Fortifyの登録画面
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // Fortify にユーザー登録処理を適用
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログインのレート制限を緩和（1分間に10回まで）
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email . $request->ip());
        });

        // カスタムログイン処理（未認証ユーザーのログインを禁止）
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Hash::check($request->password, $user->password)) {
                if (!$user->hasVerifiedEmail()) {
                    // 認証メールの再送信
                    $user->sendEmailVerificationNotification();

                    throw ValidationException::withMessages([
                        'email' => 'メール認証が完了していません。認証メールを再送信しました。',
                    ]);
                }
                return $user;
            }

            return null; // 認証失敗時
        });
    }
}
