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
        <a href="/" class="header-logo">
            <img src="{{ asset('logo.png') }}" alt="CT COACHTECH Logo" style="height: 30px; width: auto;">
        </a>
    </header>

    <main class="main-content" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <div class="form-container" style="background-color: #F5F5F5; max-width: 700px; transform: translateY(-10%);">
            <p style="text-align: center; margin-bottom: 2rem; color: #333;">
                登録していただいたメールアドレスに認証メールを送付しました。<br>
                メール認証を完了してください。
            </p>

            <div style="text-align: center; margin-bottom: 1.5rem;">
                <a href="http://localhost:8025" target="_blank" class="btn" style="background-color: #F5F5F5; color: #333; border: 1px solid #999; border-radius: 4px; padding: 0.75rem 2rem; text-decoration: none; display: inline-block;">
                    認証はこちらから
                </a>
            </div>

            <div style="text-align: center;">
                <form action="/email/verification-notification" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #0066cc; text-decoration: none; cursor: pointer; font-size: 1rem;">
                        認証メールを再送する
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

