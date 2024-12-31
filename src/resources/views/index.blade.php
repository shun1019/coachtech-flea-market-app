@extends('layouts.app')

@section('title', '商品一覧画面（トップ画面）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index-tab">
    <a href="#" class="tab-link {{ request()->query('tab', 'recommended') === 'recommended' ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('recommended-form').submit();">おすすめ</a>
    <a href="#" class="tab-link {{ request()->query('tab') === 'mylist' ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('mylist-form').submit();">マイリスト</a>
</div>

<form id="recommended-form" action="{{ route('index') }}" method="GET" style="display: none;">
    <input type="hidden" name="tab" value="recommended">
</form>
<form id="mylist-form" action="{{ route('index') }}" method="GET" style="display: none;">
    <input type="hidden" name="tab" value="mylist">
</form>

<div class="items-grid">
    @if(request()->query('tab', 'recommended') === 'recommended')
    @forelse ($items as $item)
    <div class="item-card">
        <a href="{{ route('item.detail', $item->id) }}">
            @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
            @endif
            <p class="item-name">{{ $item->name }}</p>
            <p class="item-price">¥{{ number_format($item->price) }}</p>
        </a>
    </div>
    @empty
    <p>おすすめの商品はありません。</p>
    @endforelse
    @else
    @forelse ($items as $item)
    <div class="item-card">
        <a href="{{ route('item.detail', $item->id) }}">
            @if($item->image)
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
            @endif
            <p class="item-name">{{ $item->name }}</p>
            <p class="item-price">¥{{ number_format($item->price) }}</p>
        </a>
    </div>
    @empty
    <p>表示する商品がありません。</p>
    @endforelse
    @endif
</div>
@endsection