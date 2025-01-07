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
        <h1 class="item-title">{{ $item->name }}</h1>
        <p class="brand">ブランド名</p>
        <p class="price">¥{{ number_format($item->price) }} <span>(税込)</span></p>

        <div class="icon-container">
            <div class="like-section">
                <form action="{{ route('item.like.toggle', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="like-button">
                        <div class="like-icon {{ $userLiked ? 'liked' : '' }}"></div>
                    </button>
                </form>
                <span class="like-count">{{ $item->like_count }}</span>
            </div>

            <div class="comment-section">
                <div class="comment-icon"></div>
                <span class="comment-count">{{ $comments->count() }}</span>
            </div>
        </div>

        <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn-purchase">購入手続きへ</a>

        <h3 class="section-title">商品説明</h3>
        <p class="item-description">{{ $item->description }}</p>

        <h3 class="section-title">商品の情報</h3>
        <div class="category-container">
            <p><strong>カテゴリー</strong></p>
            @foreach($item->categories as $category)
            <span class="category-tag">{{ $category->name }}</span>
            @endforeach
        </div>
        <div class="condition">
            <p><strong>商品の状態</strong></p>
            {{ $item->condition }}
        </div>

        <h3 class="section-title">コメント ({{ $comments->count() ?? 0 }})</h3>
        <div class="comments">
            @forelse($comments as $comment)
            <div class="comment">
                <div class="comment-user">
                    @if($comment->user && $comment->user->profile && $comment->user->profile->profile_image)
                    <img src="{{ asset('storage/' . $comment->user->profile->profile_image) }}" alt="ユーザー画像" class="comment-avatar">
                    @endif
                    <span class="comment-username"><strong>{{ $comment->user->username }}</strong></span>
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
            <textarea name="content">{{ old('content') }}</textarea>
            @error('content')
            <div class="comment-error">{{ $message }}</div>
            @enderror
            <button type="submit" class="btn-comment">コメントを送信する</button>
        </form>
    </div>
</div>
@endsection