@extends('layouts.app')

@section('title', '商品一覧画面（トップ画面）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index-tab">
    <a href="#">おすすめ</a>
    <a href="#">マイリスト</a>
</div>

<form>
    <input type="hidden" name="tab" value="recommends">
</form>
<form>
    <input type="hidden" name="tab" value="mylist">
</form>

<div class="item-grid">
    <div class="item-card">
    </div>
    <p>表示する商品がありません。</p>
</div>
@endsection