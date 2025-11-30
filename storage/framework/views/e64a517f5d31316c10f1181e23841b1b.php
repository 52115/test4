<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body>
    <header class="header">
        <a href="/" class="header-logo">
            <img src="<?php echo e(asset('logo.png')); ?>" alt="CT COACHTECH Logo" style="height: 30px; width: auto;">
        </a>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h1 style="text-align: center; margin-bottom: 2rem; font-size: 2rem;">会員登録</h1>

            <form action="/register" method="POST" novalidate>
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label for="name">名前</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <?php if($errors->has('password') && $errors->first('password') !== 'パスワードと一致しません'): ?>
                        <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;"><?php echo e($errors->first('password')); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">パスワード確認</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    <?php if($errors->has('password') && $errors->first('password') === 'パスワードと一致しません'): ?>
                        <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;"><?php echo e($errors->first('password')); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-black" style="width: 100%; margin-top: 1rem;">登録する</button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="/login" class="link">ログインはこちら</a>
            </div>
        </div>
    </main>
</body>
</html>

<?php /**PATH /var/www/html/resources/views/auth/register.blade.php ENDPATH**/ ?>