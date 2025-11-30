<?php $__env->startSection('title', '日次勤怠一覧'); ?>

<?php $__env->startSection('content'); ?>
<div style="padding: 3rem 14rem;">
    <h1 class="page-title"><?php echo e(date('Y年n月j日', strtotime($date))); ?>の勤怠</h1>

    <div class="month-nav" style="justify-content: space-between;">
        <a href="?date=<?php echo e($prevDate); ?>">←前日</a>
        <span class="month-display">📅 <?php echo e(date('Y/m/d', strtotime($date))); ?></span>
        <a href="?date=<?php echo e($nextDate); ?>">翌日→</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: left;">名前</th>
                    <th style="text-align: center;">出勤</th>
                    <th style="text-align: center;">退勤</th>
                    <th style="text-align: center;">休憩</th>
                    <th style="text-align: center;">合計</th>
                    <th style="text-align: center;">詳細</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="text-align: left;"><?php echo e($attendance->user->name); ?></td>
                        <td style="text-align: center;"><?php echo e($attendance->clock_in ? $attendance->clock_in->format('H:i') : ''); ?></td>
                        <td style="text-align: center;"><?php echo e($attendance->clock_out ? $attendance->clock_out->format('H:i') : ''); ?></td>
                        <td style="text-align: center;">
                            <?php if($attendance->breaks->count() > 0): ?>
                                <?php
                                    $totalBreakMinutes = $attendance->breaks->sum(function($breakTime) {
                                        if ($breakTime->break_start && $breakTime->break_end) {
                                            return $breakTime->break_start->diffInMinutes($breakTime->break_end);
                                        }
                                        return 0;
                                    });
                                    $breakHours = floor($totalBreakMinutes / 60);
                                    $breakMins = $totalBreakMinutes % 60;
                                ?>
                                <?php echo e(sprintf('%d:%02d', $breakHours, $breakMins)); ?>

                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if($attendance->clock_in && $attendance->clock_out): ?>
                                <?php
                                    $totalMinutes = $attendance->clock_in->diffInMinutes($attendance->clock_out);
                                    $totalBreakMinutes = $attendance->breaks->sum(function($breakTime) {
                                        if ($breakTime->break_start && $breakTime->break_end) {
                                            return $breakTime->break_start->diffInMinutes($breakTime->break_end);
                                        }
                                        return 0;
                                    });
                                    $totalMinutes -= $totalBreakMinutes;
                                    $totalHours = floor($totalMinutes / 60);
                                    $totalMins = $totalMinutes % 60;
                                ?>
                                <?php echo e(sprintf('%d:%02d', $totalHours, $totalMins)); ?>

                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="/admin/attendance/<?php echo e($attendance->id); ?>" class="btn btn-white" style="border: none;">詳細</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">データがありません</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/attendance/list.blade.php ENDPATH**/ ?>