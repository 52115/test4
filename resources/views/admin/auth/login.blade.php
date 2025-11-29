<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="header">
        <a href="/" class="header-logo">CT COACHTECH</a>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h1 style="text-align: center; margin-bottom: 2rem; font-size: 2rem;">管理者ログイン</h1>

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="list-style: none; padding: 0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/admin/login" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-black" style="width: 100%; margin-top: 1rem;">管理者ログインする</button>
            </form>
        </div>
    </main>
</body>
</html>

