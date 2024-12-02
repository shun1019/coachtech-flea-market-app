<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <a class="header__logo" href="/">
            <img src="{{ asset('storage/image/logo.svg') }}" alt="COACHTECH">
        </a>
        <div class="search-bar">
            <input type="text" placeholder="なにをお探しですか？">
        </div>
        <div class="header__nav">
            <form class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">ログアウト</button>
            </form>
            <form>
                <button type="submit" class="mypage-btn">マイページ</button>
            </form>
            <form>
                <button type="submit" class="sell-btn">出品</button>
            </form>
        </div>
    </header>

    @yield('content')

</body>

</html>