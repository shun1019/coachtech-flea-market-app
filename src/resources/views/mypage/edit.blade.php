@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/edit.css') }}">
@endsection

@section('content')
<div class="edit-profile__container">
    <h2 class="edit-profile__title">プロフィール設定</h2>
    <form action="{{ route('profile.update') }}" method="POST" class="edit-profile__form">
        @csrf
        <div class="edit-profile__image-section">
            @if($user->profile_image)
            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile-avatar">
            @endif
            <label class="edit-profile__image-upload-btn">
                <input type="file" name="profile_image">
            </label>
        </div>

        <div class="edit-profile__group">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" value="{{ $user->name }}">
        </div>

        <div class="edit-profile__group">
            <label for="zipcode">郵便番号</label>
            <input type="text" id="zipcode" name="zipcode" value="{{ $user->zipcode }}">
        </div>

        <div class="edit-profile__group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ $user->address }}">
        </div>

        <div class="edit-profile__group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ $user->building }}">
        </div>

        <button type="submit" class="edit-profile__submit-btn">更新する</button>
    </form>
</div>
@endsection