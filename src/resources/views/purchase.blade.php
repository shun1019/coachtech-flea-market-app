@extends('layouts.app')

@section('title', '購入手続き')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <form action="{{ route('purchase.show', ['item_id' => $item->id]) }}" method="GET" id="payment-method-form">
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
                <select name="payment_method" onchange="document.getElementById('payment-method-form').submit();">
                    <option value="">選択してください</option>
                    <option value="コンビニ払い" {{ request('payment_method') == 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                    <option value="カード払い" {{ request('payment_method') == 'カード払い' ? 'selected' : '' }}>カード払い</option>
                </select>
            </div>
        </div>
        @csrf
        <div class="purchase-form">
            <div class="purchase-address">
                <h3>配送先</h3>
                <p>〒 {{ $profile->zipcode ?? '未設定' }}</p>
                <p>{{ $profile->address ?? '未設定' }}</p>
                <p>{{ $profile->building ?? '未設定' }}</p>
                <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}">変更する</a>
            </div>
        </div>
    </form>
    <form action="{{ route('purchase.complete', ['item_id' => $item->id]) }}" method="POST">
        <div class="purchase-summary">
            <div class="summary-box">
                <div class="summary-item">
                    <p>商品代金</p>
                    <p>¥{{ number_format($item->price) }}</p>
                </div>
                <div class="summary-item">
                    <p>支払い方法</p>
                    <p>{{ request('payment_method') ?: '選択してください' }}</p>
                </div>
            </div>
            <button type="submit" class="btn-purchase">購入する</button>
        </div>
    </form>
</div>
@endsection