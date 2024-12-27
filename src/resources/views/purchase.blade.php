@extends('layouts.app')

@section('title', '購入手続き')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-form">
        <div class="purchase-item">
            @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="purchase-item__image">
            @endif
            <div class="purchase-item__info">
                <h2>{{ $item->name }}</h2>
                <p class="price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>
        <div class="purchase-payment">
            <h3>支払い方法</h3>
            <select>
                <option value="">選択してください</option>
                <option value="convenience_store">コンビニ払い</option>
                <option value="credit_card">カード払い</option>
            </select>
        </div>
        <div class="purchase-address">
            <h3>配送先</h3>
            <p>〒 {{ $profile->zipcode }}</p>
            <p>{{ $profile->address }}</p>
            <p>{{ $profile->building }}</p>
            <a href="{{ route('profile.edit') }}">変更する</a>
        </div>
    </div>
    <div class="purchase-summary">
        <div class="summary-box">
            <div class="summary-item">
                <p>商品代金</p>
                <p>¥{{ number_format($item->price) }}</p>
            </div>
            <div class="summary-item">
                <p>支払い方法</p>
                <p>コンビニ払い</p>
            </div>
        </div>
        <button class="btn-purchase">購入する</button>
    </div>
</div>
@endsection