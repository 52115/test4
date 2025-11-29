<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理者 - 勤怠管理システム')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    @auth
    <header class="header">
        <a href="/admin/attendance/list" class="header-logo">
            <img src="{{ asset('logo.png') }}" alt="CT COACHTECH" style="height: 40px; width: auto;">
        </a>
        <nav class="header-nav">
            <a href="/admin/attendance/list">勤怠一覧</a>
            <a href="/admin/staff/list">スタッフ一覧</a>
            <a href="/stamp_correction_request/list">申請一覧</a>
            <form action="/admin/logout" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #ffffff; cursor: pointer; font-size: 1rem;">ログアウト</button>
            </form>
        </nav>
    </header>
    @endauth

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

