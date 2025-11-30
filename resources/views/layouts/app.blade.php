<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '勤怠管理システム')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    @auth
    <header class="header">
        <a href="/attendance" class="header-logo">
            <img src="{{ asset('logo.png') }}" alt="CT COACHTECH" style="height: 30px; width: auto;">
        </a>
        <nav class="header-nav">
            <a href="/attendance">勤怠</a>
            <a href="/attendance/list">勤怠一覧</a>
            <a href="/stamp_correction_request/list">申請</a>
            <form action="/logout" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #ffffff; cursor: pointer; font-size: 1rem;">ログアウト</button>
            </form>
        </nav>
    </header>
    @endauth

    <main class="main-content">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

