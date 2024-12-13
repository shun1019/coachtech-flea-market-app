@extends('layouts.auth')

@section('title', '会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="register-form">
    <h1>会員登録</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="register-form__group">
            <label for="username">ユーザー名</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}">
            @error('username')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="register-form__group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}">
            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="register-form__group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password">
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="register-form__group">
            <label for="password_confirmation">確認用パスワード</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
            @error('password_confirmation')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button class="register-btn" type="submit">登録する</button>

        <p><a href="{{ route('login') }}">ログインはこちら</a></p>
    </form>
</div>
@endsection