<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 既存のデータをクリア（オプション）
        // User::truncate();
        // Attendance::truncate();
        // BreakTime::truncate();

        // 管理者ユーザーを作成
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 一般ユーザーを5人作成
        $users = User::factory()->count(5)->create([
            'email_verified_at' => now(),
        ]);

        // 過去30日間の勤怠データを作成
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        foreach ($users as $user) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // 週末（土日）はスキップ（オプション）
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                // 80%の確率で出勤記録を作成
                if (rand(1, 100) <= 80) {
                    // 出勤時間（8:00-10:00の間でランダム）
                    $clockInHour = rand(8, 10);
                    $clockInMinute = rand(0, 59);
                    $clockIn = $currentDate->copy()->setTime($clockInHour, $clockInMinute);

                    // 退勤時間（17:00-20:00の間でランダム）
                    $clockOutHour = rand(17, 20);
                    $clockOutMinute = rand(0, 59);
                    $clockOut = $currentDate->copy()->setTime($clockOutHour, $clockOutMinute);

                    // ステータスを決定
                    $status = 'clocked_out';
                    if ($clockOut->isToday() && $clockOut->isFuture()) {
                        $status = 'on_break';
                    } elseif ($clockOut->isToday() && $clockIn->isPast() && $clockOut->isFuture()) {
                        $status = 'working';
                    }

                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut->isFuture() ? null : $clockOut,
                        'status' => $status,
                        'remarks' => rand(1, 10) <= 2 ? '遅延のため' : null, // 20%の確率で備考あり
                    ]);

                    // 休憩時間を1-2回作成（70%の確率）
                    if (rand(1, 100) <= 70) {
                        $breakCount = rand(1, 2);
                        
                        for ($i = 0; $i < $breakCount; $i++) {
                            // 休憩開始時間（12:00-14:00の間でランダム）
                            $breakStartHour = rand(12, 14);
                            $breakStartMinute = rand(0, 59);
                            $breakStart = $currentDate->copy()->setTime($breakStartHour, $breakStartMinute);

                            // 休憩終了時間（休憩開始から30分-1時間30分後）
                            $breakDuration = rand(30, 90);
                            $breakEnd = $breakStart->copy()->addMinutes($breakDuration);

                            // 出勤時間と退勤時間の範囲内に収まるように調整
                            if ($breakStart->lt($clockIn)) {
                                $breakStart = $clockIn->copy()->addHours(3);
                            }
                            if ($breakEnd->gt($clockOut) && $clockOut->isPast()) {
                                $breakEnd = $clockOut->copy()->subMinutes(30);
                            }

                            BreakTime::create([
                                'attendance_id' => $attendance->id,
                                'break_start' => $breakStart,
                                'break_end' => $breakEnd,
                            ]);
                        }
                    }
                }

                $currentDate->addDay();
            }
        }

        $this->command->info('ダミーデータの作成が完了しました！');
        $this->command->info('管理者ユーザー: admin@example.com / password123');
        $this->command->info('一般ユーザー: ' . $users->count() . '人作成しました');
    }
}

