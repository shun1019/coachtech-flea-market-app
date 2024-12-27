@extends('layouts.app')

@section('title', '住所の変更')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endsection

@section('content')
<div class="address-edit-container">
    <h1>住所の変更</h1>
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="address-edit__form">
            <label for="zipcode">郵便番号</label>
            <input type="text" id="zipcode" name="zipcode" value="{{ old('zipcode', $profile->zipcode ?? '') }}">
            @error('zipcode')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="address-edit__form">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="address-edit__form">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', $profile->building ?? '') }}">
            @error('building')
            <div class="form-error">{{ $$message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">保存する</button>
    </form>
</div>
@endsection