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
        <h2 class="profile-name">{{ e($user->username) }}</h2>
        <a href="{{ route('profile.edit') }}" class="edit-profile-btn">プロフィールを編集</a>
    </div>

    <div class="tabs">
        <a href="{{ route('profile.index', ['tab' => 'sell']) }}" class="{{ $tab === 'sell' ? 'active-sell' : '' }}">出品した商品</a>
        <a href="{{ route('profile.index', ['tab' => 'buy']) }}" class="{{ $tab === 'buy' ? 'active-buy' : '' }}">購入した商品</a>
    </div>

    @if($tab === 'sell')
    <div class="tab-content">
        <div class="items-grid">
            @forelse($listedItems as $item)
            <div class="item-card">
                <a href="{{ route('item.home', $item->id) }}">
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
    </div>
    @elseif($tab === 'buy')
    <div class="tab-content">
        <div class="items-grid">
            @forelse($purchasedItems as $item)
            <div class="item-card">
                <a href="{{ route('item.home', $item->id) }}">
                    @if($item->image && Storage::exists('public/' . $item->image))
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ e($item->name) }}" class="item-image">
                    @endif
                    <p class="item-name">{{ e($item->name) }}</p>
                </a>
            </div>
            @empty
            <p>購入した商品はありません。</p>
            @endforelse
        </div>
    </div>
    @endif
</div>
@endsection