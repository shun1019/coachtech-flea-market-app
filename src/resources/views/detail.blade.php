@extends('layouts.app')

@section('title', '商品詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__image">
        @if($item->image)
        <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像">
        @endif
    </div>
    <div class="item-detail__info">
        <h2 class="item-title">{{ $item->name }}</h2>
        <p class="brand">ブランド名</p>
        <p class="price">¥{{ number_format($item->price) }} <span>(税込)</span></p>
        <div class="item-detail__likes">
            <span>☆</span>
            <span>{{ $item->like_count }}</span>
            <span>コメント {{ $comments->count() ?? 0 }}</span>
        </div>

        <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn-purchase">購入手続きへ</a>

        <h3 class="section-title">商品説明</h3>
        <p class="item-description">{{ $item->description }}</p>

        <h3 class="section-title">商品の情報</h3>
        <p><strong>カテゴリー:</strong> {{ $item->category->name ?? '未分類' }}</p>
        <p><strong>商品の状態:</strong> {{ $item->condition }}</p>

        <h3 class="section-title">コメント ({{ $comments->count() ?? 0 }})</h3>
        <div class="comments">
            @forelse($comments as $comment)
            <div class="comment">
                <div class="comment-user">
                    @if($user && $user->profile && $user->profile->profile_image)
                    <img src="{{ asset('storage/' . $user->profile->profile_image) }}" alt="ユーザー画像" class="comment-avatar">
                    @endif
                    <p><strong>{{ $comment->user->name }}</strong></p>
                </div>
                <p class="comment-content">{{ $comment->content }}</p>
            </div>
            @empty
            <p>コメントはありません。</p>
            @endforelse
        </div>

        <form action="{{ route('comment.store', $item->id) }}" method="POST">
            @csrf
            <div class="textarea-section__title">商品へのコメント</div>
            <textarea name="content" required></textarea>
            <button type="submit" class="btn-comment">コメントを送信する</button>
        </form>
    </div>
</div>
@endsection