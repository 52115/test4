<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * テスト4: 現在の日時情報がUIと同じ形式で出力されている
     */
    public function test_clock_page_displays_current_datetime(): void
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/attendance');
        
        $response->assertStatus(200);
        $response->assertViewIs('attendance.clock');
        
        // 現在の日時が表示されていることを確認
        $now = Carbon::now();
        $response->assertSee($now->format('Y年m月d日'));
    }

    /**
     * テスト5-1: 勤務外の場合、勤怠ステータスが正しく表示される
     */
    public function test_clock_page_displays_status_off_duty(): void
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/attendance');
        
        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /**
     * テスト5-2: 出勤中の場合、勤怠ステータスが正しく表示される
     */
    public function test_clock_page_displays_status_working(): void
    {
        $this->actingAs($this->user);
        
        // 出勤記録を作成
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(2),
            'status' => 'working',
        ]);
        
        $response = $this->get('/attendance');
        
        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    /**
     * テスト5-3: 休憩中の場合、勤怠ステータスが正しく表示される
     */
    public function test_clock_page_displays_status_break(): void
    {
        $this->actingAs($this->user);
        
        // 出勤・休憩記録を作成
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(2),
            'status' => 'on_break',
        ]);
        
        $response = $this->get('/attendance');
        
        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    /**
     * テスト5-4: 退勤済の場合、勤怠ステータスが正しく表示される
     */
    public function test_clock_page_displays_status_completed(): void
    {
        $this->actingAs($this->user);
        
        // 退勤済みの記録を作成
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHours(1),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance');
        
        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }

    /**
     * テスト6-1: 出勤ボタンが正しく機能する
     */
    public function test_clock_in_functionality(): void
    {
        $this->actingAs($this->user);
        
        $response = $this->post('/attendance/clock-in');
        
        $response->assertRedirect('/attendance');
        
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'status' => 'working',
        ]);
    }

    /**
     * テスト6-2: 出勤は一日一回のみできる
     */
    public function test_clock_in_only_once_per_day(): void
    {
        $this->actingAs($this->user);
        
        // 既に出勤済みの記録を作成
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(2),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance');
        
        $response->assertStatus(200);
        $response->assertDontSee('出勤');
    }

    /**
     * テスト6-3: 出勤時刻が勤怠一覧画面で確認できる
     */
    public function test_clock_in_time_displayed_in_list(): void
    {
        $this->actingAs($this->user);
        
        $clockInTime = Carbon::now()->subHours(2);
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => $clockInTime,
            'status' => 'working',
        ]);
        
        $response = $this->get('/attendance/list');
        
        $response->assertStatus(200);
        $response->assertSee($clockInTime->format('H:i'));
    }

    /**
     * テスト7-1: 休憩ボタンが正しく機能する
     */
    public function test_break_start_functionality(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(2),
            'status' => 'working',
        ]);
        
        $response = $this->post('/attendance/break-start');
        
        $response->assertRedirect('/attendance');
        
        $attendance->refresh();
        $this->assertEquals('on_break', $attendance->status);
    }

    /**
     * テスト7-2: 休憩は一日に何回でもできる
     */
    public function test_break_can_be_taken_multiple_times(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(4),
            'status' => 'working',
        ]);
        
        // 1回目の休憩
        $this->post('/attendance/break-start');
        $this->post('/attendance/break-end');
        
        // 2回目の休憩が可能か確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    /**
     * テスト7-3: 休憩戻ボタンが正しく機能する
     */
    public function test_break_end_functionality(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(2),
            'status' => 'on_break',
        ]);
        
        $response = $this->post('/attendance/break-end');
        
        $response->assertRedirect('/attendance');
        
        $attendance->refresh();
        $this->assertEquals('working', $attendance->status);
    }

    /**
     * テスト7-4: 休憩戻は一日に何回でもできる
     */
    public function test_break_end_can_be_done_multiple_times(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(4),
            'status' => 'on_break',
        ]);
        
        // 1回目の休憩戻
        $this->post('/attendance/break-end');
        
        // 再度休憩に入る
        $this->post('/attendance/break-start');
        
        // 2回目の休憩戻が可能か確認
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    /**
     * テスト7-5: 休憩時刻が勤怠一覧画面で確認できる
     */
    public function test_break_time_displayed_in_list(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(4),
            'status' => 'working',
        ]);
        
        $breakStart = Carbon::now()->subHours(2);
        $breakEnd = Carbon::now()->subHour();
        
        $attendance->breaks()->create([
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ]);
        
        $response = $this->get('/attendance/list');
        
        $response->assertStatus(200);
        // 休憩時間は合計時間として表示される（1時間 = 1:00）
        $breakMinutes = $breakStart->diffInMinutes($breakEnd);
        $breakHours = floor($breakMinutes / 60);
        $breakMins = $breakMinutes % 60;
        $response->assertSee(sprintf('%d:%02d', $breakHours, $breakMins));
    }

    /**
     * テスト8-1: 退勤ボタンが正しく機能する
     */
    public function test_clock_out_functionality(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'working',
        ]);
        
        $response = $this->post('/attendance/clock-out');
        
        $response->assertRedirect('/attendance');
        
        $attendance->refresh();
        $this->assertEquals('clocked_out', $attendance->status);
        $this->assertNotNull($attendance->clock_out);
    }

    /**
     * テスト8-2: 退勤時刻が勤怠一覧画面で確認できる
     */
    public function test_clock_out_time_displayed_in_list(): void
    {
        $this->actingAs($this->user);
        
        $clockOutTime = Carbon::now()->subHour();
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => $clockOutTime,
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/list');
        
        $response->assertStatus(200);
        $response->assertSee($clockOutTime->format('H:i'));
    }
}

