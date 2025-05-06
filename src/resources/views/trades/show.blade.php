@extends('layouts.app')

@section('title', 'お取引画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="trade-container">
    <aside class="sidebar-nav">
        <h3 class="sidebar-title">その他の取引</h3>
        <ul class="nav-list">
            @foreach($otherTrades as $otherTrade)
            @if($otherTrade->status !== 'completed')
            <li>
                <a href="{{ route('mypage.trades.show', ['trade' => $otherTrade->id]) }}"
                    class="nav-link {{ $trade->id === $otherTrade->id ? 'active' : '' }}">
                    {{ $otherTrade->item->name }}
                </a>
            </li>
            @endif
            @endforeach
        </ul>
    </aside>

    <main class="trade-header">
        <div class="header-area">
            <div class="header-left">
                @php
                $userId = Auth::id();
                $isBuyer = $trade->buyer_id === $userId;
                $isSeller = $trade->seller_id === $userId;
                $hasRated = $trade->ratings->where('rater_id', $userId)->isNotEmpty();
                $buyerHasRated = $trade->ratings->where('rater_id', $trade->buyer_id)->isNotEmpty();
                $showModal = !$hasRated && (
                ($isBuyer && request()->query('rate')) ||
                ($isSeller && session('show_rating_modal'))
                );
                $otherUser = $isBuyer ? $trade->seller : $trade->buyer;
                @endphp

                @if($otherUser->profile && $otherUser->profile->profile_image)
                <img src="{{ Storage::url($otherUser->profile->profile_image) }}" alt="プロフィール画像" class="profile-avatar">
                @endif

                <h2 class="trade-title">「{{ $otherUser->username }}」さんとの取引画面</h2>
            </div>

            {{-- 評価モーダル --}}
            @if($showModal)
            <div class="rating-modal-overlay">
                <div class="rating-modal">
                    <p class="rating-title">取引が完了しました。</p>
                    <p class="rating-subtitle">今回の取引相手はどうでしたか？</p>
                    <form action="{{ route('trade.rate', $trade->id) }}" method="POST">
                        @csrf
                        <div class="star-rating">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                            <label for="star{{ $i }}">★</label>
                            @endfor
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="submit-rating-btn">送信する</button>
                        </div>
                    </form>
                </div>
            </div>
            @elseif($isBuyer && !$hasRated && $trade->status !== 'completed')
            <form method="GET" action="{{ route('trade.show', ['trade' => $trade->id]) }}">
                <input type="hidden" name="rate" value="1">
                <button type="submit" class="complete-btn">取引を完了する</button>
            </form>
            @elseif($hasRated)
            <div class="complete-btn" style="background-color: #ccc; cursor: default;">評価済み</div>
            @endif
        </div>

        {{-- 商品情報 --}}
        <div class="item-info">
            <div class="item-box">
                <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像" class="item-image">
                <div class="item-detail">
                    <h5 class="item-name">{{ $item->name }}</h5>
                    <p class="item-price">{{ number_format($item->price) }}円</p>
                </div>
            </div>
        </div>

        {{-- チャットメッセージ --}}
        <div class="message-area">
            @foreach($chatMessages as $message)
            @php $isSellerMessage = $message->user_id === $trade->seller_id; @endphp
            <div class="message-box mb-3 {{ $isSellerMessage ? 'text-left' : 'text-right' }}">
                <div class="message-content {{ $isSellerMessage ? 'other-message' : 'own-message' }}">
                    @if($message->user->profile && $message->user->profile->profile_image)
                    <div class="user-avatar">
                        <img src="{{ asset('storage/' . $message->user->profile->profile_image) }}" class="profile-image" alt="プロフィール画像">
                    </div>
                    @endif

                    <div class="message-bubble">
                        <div class="user-name">{{ $message->user->username }}</div>

                        @if ($message->image_path)
                        <div class="message-image">
                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="添付画像" class="attached-image">
                        </div>
                        @endif

                        <div class="message-text">{{ $message->body }}</div>

                        @if ($message->user_id === $userId)
                        <div class="message-actions">
                            <form method="GET" action="{{ route('trade.show', ['trade' => $trade->id]) }}">
                                <input type="hidden" name="edit" value="{{ $message->id }}">
                                <button type="submit" class="edit-toggle-btn">編集</button>
                            </form>

                            @if(request('edit') == $message->id)
                            <form action="{{ route('chat.update', $message->id) }}" method="POST" enctype="multipart/form-data" class="update-message">
                                @csrf
                                @method('PUT')
                                <input type="text" name="body" value="{{ $message->body }}" class="message-input-inline">
                                <input type="file" name="image" class="file-input-inline">
                                <button type="submit" class="edit-submit-btn">更新</button>
                            </form>
                            @endif

                            <form action="{{ route('chat.destroy', $message->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">削除</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if ($errors->any())
        <div class="validation-errors">
            <ul>
                @foreach ($errors->all() as $error)
                <li class="error-message">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- チャットフォーム --}}
        <div class="message-form">
            <form action="{{ route('chat.store', $trade->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="chat-box">
                    <input type="text" name="body" class="message-input"
                        placeholder="取引メッセージを記入してください"
                        value="{{ old('body', session("chat_draft_{$trade->id}")) }}">
                    <label for="file-upload-new" class="file-label">画像を追加</label>
                    <input type="file" name="image" id="file-upload-new" class="file-input">
                    <button type="submit" class="send-icon-btn">
                        <img src="{{ asset('storage/image/チャット送信アイコン.jpg') }}" alt="送信" class="send-icon">
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection