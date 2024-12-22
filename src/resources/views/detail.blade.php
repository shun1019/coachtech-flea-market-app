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
            <span>コメント {{ $comments->count() }}</span>
        </div>

        <!-- 購入手続きへボタン -->
        @if(Auth::check())
        <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn-purchase">購入手続きへ</a>
        @else
        <a href="{{ route('login') }}" class="btn-purchase">購入手続きへ</a>
        @endif

        <h3 class="section-title">商品説明</h3>
        <p class="item-description">{{ $item->description }}</p>

        <h3 class="section-title">商品の情報</h3>
        <p><strong>カテゴリー:</strong> {{ $item->category->name }}</p>
        <p><strong>商品の状態:</strong> {{ $item->condition }}</p>

        <h3 class="section-title">コメント ({{ $comments->count() }})</h3>
        <div class="comments">
            @forelse($comments as $comment)
            <div class="comment">
                <div class="comment-user">
                    @if($comment->user->profile_image)
                    <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="ユーザー画像" class="comment-avatar">
                    @endif
                    <p><strong>{{ $comment->user->name }}</strong></p>
                </div>
                <p class="comment-content">{{ $comment->content }}</p>
            </div>
            @empty
            <p>コメントはありません。</p>
            @endforelse
        </div>

        @if(Auth::check())
        <form action="{{ route('comment.store', $item->id) }}" method="POST">
            @csrf
            <div class="textarea-section__title">商品へのコメント</div>
            <textarea name="content"></textarea>
            <button type="submit" class="btn-comment">コメントを送信する</button>
        </form>
        @else
        <p>コメントを投稿するには<a href="{{ route('login') }}">ログイン</a>が必要です。</p>
        @endif
    </div>
</div>
@endsection