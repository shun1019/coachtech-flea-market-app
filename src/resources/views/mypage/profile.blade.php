@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h2 class="profile-title">プロフィール設定</h2>
    <form class="profile-form">
        <div class="profile-image__section">
            <label for="image-upload__btn">
                <input type="file" name="profile_image">
                画像を選択する
            </label>
        </div>

        <div class="profile-form__group">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" value="{{ old('username', auth()->user()->username) }}">
        </div>

        <div class="profile-form__group">
            <label for="zipcode">郵便番号</label>
            <input type="zipcode" id="zipcode" value="{{ old('zipcode', auth()->user()->zipcode) }}">
        </div>

        <div class="profile-form__group">
            <label for="address">住所</label>
            <input type="text" id="address" value="{{ old('address', auth()->user()->address) }}">
        </div>

        <div class="profile-form__group">
            <label for="building"></label>
            <input type="text" id="building" value="{{ old('building', auth()->user()->building) }}">
        </div>

        <button type="submit" class="submit-btn">更新する</button>
    </form>
</div>
@endsection