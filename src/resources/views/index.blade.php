@extends('layouts.app')

@section('title', '商品一覧画面（トップ画面）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index-container">
    <div class="index-tab">
        <a href="{{ route('index', ['tab' => 'recommended', 'search' => request('search')]) }}"
            class="tab-link {{ request()->query('tab', 'recommended') === 'recommended' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('index', ['tab' => 'mylist', 'search' => request('search')]) }}"
            class="tab-link {{ request()->query('tab') === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="items-grid">
        @forelse ($items as $item)
        <div class="item-card">
            <a href="{{ route('item.detail', $item->id) }}">
                @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ e($item->name) }}" class="item-image">
                @endif
                <p class="item-name">{{ e($item->name) }}</p>

                @if($item->status === 'sold')
                <span class="sold-label">SOLD</span>
                @endif
            </a>
        </div>
        @empty
        <p>{{ auth()->check() && request()->query('tab', 'recommended') === 'recommended' ? 'おすすめの商品はありません。' : 'いいねした商品がありません。' }}</p>
        @endforelse
    </div>

    <div class="pagination">
        {{ $items->appends(['search' => request('search'), 'tab' => request('tab')])->links() }}
    </div>
</div>
@endsection