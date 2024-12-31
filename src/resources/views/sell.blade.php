@extends('layouts.app')

@section('title', '商品の出品')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-form">
    <h1 class="address-edit__title">商品の出品</h1>

    <form action="{{ route('store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="sell-form__group">
            <label for="item-image">商品画像</label>
            <div class="image-upload-wrapper">
                <input type="file" id="image" name="image" accept="image/*" class="sell-form__image">
                <label for="image" class="image-upload-label">画像を選択する</label>
            </div>
            @error('image')
            <div class="sell-form-error">{{ $message }}</div>
            @enderror
        </div>

        <h2>商品の詳細</h2>

        <div class="sell-form__group">
            <label for="category">カテゴリー</label>
            <div class="sell-category-tags">
                @foreach ($categories as $category)
                <label>
                    <input type="radio" name="category_id" value="{{ $category->id }}" required>
                    <span class="category-tag">{{ $category->name }}</span>
                </label>
                @endforeach
            </div>
            @error('category_id')
            <div class="sell-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="sell-form__group">
            <label for="condition">商品の状態</label>
            <select id="condition" name="condition">
                <option value="">選択してください</option>
                <option value="良好">良好</option>
                <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option value="状態が悪い">状態が悪い</option>
            </select>
            @error('condition')
            <div class="sell-form-error">{{ $message }}</div>
            @enderror
        </div>

        <h2>商品名と説明</h2>

        <div class="sell-form__group">
            <label for="name">商品名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            @error('name')
            <div class="sell-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="sell-form__group">
            <label for="description">商品の説明</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
            <div class="sell-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="sell-form__group">
            <label for="price">販売価格</label>
            <input type="number" id="price" name="price" value="{{ old('price') }}" placeholder="¥">
            @error('price')
            <div class="sell-form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="sell-btn-submit">出品する</button>
    </form>
</div>
@endsection