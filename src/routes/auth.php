<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// 認証関連のビュー設定
Fortify::loginView(fn() => view('auth.login'));
Fortify::registerView(fn() => view('auth.register'));
Fortify::verifyEmailView(fn() => view('auth.verify-email'));

// カスタムログイン処理（メール認証が完了していない場合、エラーを表示）
Fortify::authenticateUsing(function (Request $request) {
    $user = \App\Models\User::where('email', $request->email)->first();

    if ($user && \Hash::check($request->password, $user->password)) {
        if (!$user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => 'メール認証が完了していません。認証メールを確認してください。',
            ]);
        }
        return $user;
    }

    return null;
});

// 認証待機ページの表示（未認証ユーザー用）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール認証処理（リンクをクリックした後の処理）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/mypage/profile/edit')->with('success', 'メール認証が完了しました！');
})->middleware('signed')->name('verification.verify');

// 認証メールの再送信処理
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ログアウト処理
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');
