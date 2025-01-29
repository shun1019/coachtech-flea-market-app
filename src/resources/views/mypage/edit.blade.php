@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/edit.css') }}">
@endsection

@section('content')
<div class="edit-profile__container">
    <h1 class="edit-profile__title">プロフィール設定</h1>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="edit-profile__form">
        @csrf

        <div class="edit-profile__image-section">
            @if($user->profile && $user->profile->profile_image)
            <img id="profile-preview" src="{{ Storage::url($user->profile->profile_image) }}" alt="プロフィール画像" class="profile-avatar">
            @else
            <img id="profile-preview" src="{{ asset('storage/default-avatar.png') }}" alt="プロフィール画像" class="profile-avatar">
            @endif

            <input type="file" name="profile_image" id="profile_image">
            <label for="profile_image" class="image-upload-label">画像を選択する</label>
            @error('profile_image')
            <p class="edit-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="edit-profile__group">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}">
            @error('username')
            <p class="edit-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="edit-profile__group">
            <label for="zipcode">郵便番号</label>
            <input type="text" id="zipcode" name="zipcode" value="{{ old('zipcode', $user->profile->zipcode ?? '') }}">
            @error('zipcode')
            <p class="edit-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="edit-profile__group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $user->profile->address ?? '') }}">
            @error('address')
            <p class="edit-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="edit-profile__group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', $user->profile->building ?? '') }}">
            @error('building')
            <p class="edit-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="edit-profile__submit-btn">更新する</button>
    </form>
</div>
@endsection

@section('js')
<script src="{{ asset('js/profile.js') }}"></script>
@endsection