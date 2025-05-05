@extends('layouts.app')

@section('title', 'マイページ')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-header">
        @if($user->profile && $user->profile->profile_image)
        <img src="{{ Storage::url($user->profile->profile_image) }}" alt="プロフィール画像" class="profile-avatar">
        @endif
        <h2 class="profile-name">{{ $user->username }}</h2>

        @if(isset($averageRating))
        <p class="average-rating">評価平均：{{ $averageRating }} / 5</p>
        @endif

        <a href="{{ route('profile.edit') }}" class="edit-profile-btn">プロフィールを編集</a>
    </div>

    <div class="tabs">
        <a href="{{ route('profile.index', ['tab' => 'sell']) }}" class="{{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('profile.index', ['tab' => 'buy']) }}" class="{{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ route('profile.index', ['tab' => 'trading']) }}" class="{{ $tab === 'trading' ? 'active' : '' }}">
            取引中の商品 <span class="badge">{{ isset($tradingTrades) ? $tradingTrades->count() : 0 }}</span>
        </a>
    </div>

    <div class="tab-content">
        @if($tab === 'sell')
        <div class="items-grid">
            @forelse($listedItems as $item)
            <div class="item-card">
                @if($item->status === 'sold')
                <span class="sold-label">SOLD</span>
                @endif
                <a href="{{ route('item.detail', $item->id) }}">
                    @if($item->image && Storage::exists('public/' . $item->image))
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ e($item->name) }}" class="item-image">
                    @endif
                    <p class="item-name">{{ e($item->name) }}</p>
                </a>
            </div>
            @empty
            <p>出品した商品はありません。</p>
            @endforelse
        </div>
        <div class="pagination">
            {{ $listedItems->appends(['tab' => 'sell'])->links() }}
        </div>

        @elseif($tab === 'buy')
        <div class="items-grid">
            @forelse($purchasedItems as $purchase)
            @php $item = $purchase->item; @endphp
            <div class="item-card">
                @if($item && $item->status === 'sold')
                <span class="sold-label">SOLD</span>
                @endif
                <a href="{{ route('item.detail', $item->id) }}">
                    @if($item && $item->image && Storage::exists('public/' . $item->image))
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ e($item->name) }}" class="item-image">
                    @endif
                    <p class="item-name">{{ e($item->name) }}</p>
                </a>
            </div>
            @empty
            <p>購入した商品はありません。</p>
            @endforelse
        </div>
        <div class="pagination">
            {{ $purchasedItems->appends(['tab' => 'buy'])->links() }}
        </div>

        @elseif($tab === 'trading')
        <div class="items-grid">
            @forelse($tradingTrades as $trade)
            @php
            $item = $trade->item;
            $unread = $trade->chatMessages->where('is_read', false)->where('user_id', '!=', $user->id)->count();
            @endphp
            <div class="item-card">
                @if($unread > 0)
                <span class="notification-badge">{{ $unread }}</span>
                @endif
                <a href="{{ route('trade.show', ['trade' => $trade->id]) }}">
                    @if($item->image && Storage::exists('public/' . $item->image))
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ e($item->name) }}" class="item-image">
                    @endif
                    <p class="item-name">{{ e($item->name) }}</p>
                </a>
            </div>
            @empty
            <p>取引中の商品はありません。</p>
            @endforelse
        </div>
        @endif
    </div>
</div>
@endsection