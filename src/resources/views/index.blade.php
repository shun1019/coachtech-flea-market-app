@extends('layouts.app')

@section('title', '商品一覧画面（トップ画面）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index-container">
    <div class="index-tab">
        <a href="{{ auth()->check() ? route('index', ['tab' => 'recommended']) : '#' }}"
            class="tab-link {{ request()->query('tab', 'recommended') === 'recommended' ? 'active' : '' }}"
            {{ auth()->check() ? '' : 'onclick="return false;"' }}>
            おすすめ
        </a>
        <a href="{{ auth()->check() ? route('index', ['tab' => 'mylist']) : '#' }}"
            class="tab-link {{ request()->query('tab') === 'mylist' ? 'active' : '' }}"
            {{ auth()->check() ? '' : 'onclick="return false;"' }}>
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
        <p>{{ auth()->check() && request()->query('tab', 'recommended') === 'recommended' ? 'おすすめの商品はありません。' : '表示する商品がありません。' }}</p>
        @endforelse
    </div>

    <div class="pagination">
        {{ $items->links() }}
    </div>
</div>
@endsection