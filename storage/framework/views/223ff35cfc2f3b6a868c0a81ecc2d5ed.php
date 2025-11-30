<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', '管理者 - 勤怠管理システム'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <?php if(auth()->guard()->check()): ?>
    <header class="header">
        <a href="/admin/attendance/list" class="header-logo">
            <img src="<?php echo e(asset('logo.png')); ?>" alt="CT COACHTECH" style="height: 30px; width: auto;">
        </a>
        <nav class="header-nav">
            <a href="/admin/attendance/list">勤怠一覧</a>
            <a href="/admin/staff/list">スタッフ一覧</a>
            <a href="/admin/stamp_correction_request/list">申請一覧</a>
            <form action="/admin/logout" method="POST" style="display: inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" style="background: none; border: none; color: #ffffff; cursor: pointer; font-size: 1rem;">ログアウト</button>
            </form>
        </nav>
    </header>
    <?php endif; ?>

    <main class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH /var/www/html/resources/views/layouts/admin.blade.php ENDPATH**/ ?>