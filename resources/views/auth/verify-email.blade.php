<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メール認証</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="header">
        <a href="/" class="header-logo">CT COACHTECH</a>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h1 style="text-align: center; margin-bottom: 2rem; font-size: 2rem;">メール認証</h1>

            <p style="text-align: center; margin-bottom: 2rem;">
                メールアドレスの確認が必要です。登録されたメールアドレスに認証メールを送信しました。
            </p>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form action="/email/verification-notification" method="POST">
                @csrf
                <button type="submit" class="btn btn-black" style="width: 100%;">認証メール再送</button>
            </form>
        </div>
    </main>
</body>
</html>

