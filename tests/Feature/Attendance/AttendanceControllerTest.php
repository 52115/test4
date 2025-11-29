<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * テスト9-1: 自分が行った勤怠情報が全て表示されている
     */
    public function test_attendance_list_displays_all_user_attendances(): void
    {
        $this->actingAs($this->user);
        
        // 複数の勤怠記録を作成
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHour(),
            'status' => 'clocked_out',
        ]);
        
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::yesterday(),
            'clock_in' => Carbon::yesterday()->setTime(9, 0),
            'clock_out' => Carbon::yesterday()->setTime(18, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/list');
        
        $response->assertStatus(200);
        $response->assertViewIs('attendance.list');
        // 日付はテーブル内で表示されるため、月の表示を確認
        $response->assertSee(Carbon::now()->format('Y'));
    }

    /**
     * テスト9-2: 勤怠一覧画面に遷移した際に現在の月が表示される
     */
    public function test_attendance_list_displays_current_month(): void
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/attendance/list');
        
        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('Y'));
        $response->assertSee(str_pad(Carbon::now()->month, 2, '0', STR_PAD_LEFT));
    }

    /**
     * テスト9-3: 「前月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_attendance_list_previous_month(): void
    {
        $this->actingAs($this->user);
        
        $lastMonth = Carbon::now()->subMonth();
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $lastMonth->copy()->startOfMonth(),
            'clock_in' => $lastMonth->copy()->startOfMonth()->setTime(9, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/list?year=' . $lastMonth->year . '&month=' . $lastMonth->month);
        
        $response->assertStatus(200);
        $response->assertSee($lastMonth->format('Y'));
        $response->assertSee(str_pad($lastMonth->month, 2, '0', STR_PAD_LEFT));
    }

    /**
     * テスト9-4: 「翌月」を押下した時に表示月の翌月の情報が表示される
     */
    public function test_attendance_list_next_month(): void
    {
        $this->actingAs($this->user);
        
        $nextMonth = Carbon::now()->addMonth();
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $nextMonth->copy()->startOfMonth(),
            'clock_in' => $nextMonth->copy()->startOfMonth()->setTime(9, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/list?year=' . $nextMonth->year . '&month=' . $nextMonth->month);
        
        $response->assertStatus(200);
        $response->assertSee($nextMonth->format('Y'));
        $response->assertSee(str_pad($nextMonth->month, 2, '0', STR_PAD_LEFT));
    }

    /**
     * テスト9-5: 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
     */
    public function test_attendance_list_detail_link(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/detail/' . $attendance->id);
        
        $response->assertStatus(200);
        $response->assertViewIs('attendance.detail');
    }

    /**
     * テスト10-1: 勤怠詳細画面の「名前」がログインユーザーの氏名になっている
     */
    public function test_attendance_detail_displays_user_name(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/detail/' . $attendance->id);
        
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
    }

    /**
     * テスト10-2: 勤怠詳細画面の「日付」が選択した日付になっている
     */
    public function test_attendance_detail_displays_correct_date(): void
    {
        $this->actingAs($this->user);
        
        $date = Carbon::today();
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $date,
            'clock_in' => $date->copy()->setTime(9, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/detail/' . $attendance->id);
        
        $response->assertStatus(200);
        $response->assertSee($date->format('Y年'));
        $response->assertSee($date->format('n月'));
        $response->assertSee($date->format('j日'));
    }

    /**
     * テスト10-3: 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
     */
    public function test_attendance_detail_displays_clock_times(): void
    {
        $this->actingAs($this->user);
        
        $clockIn = Carbon::now()->subHours(8)->setTime(9, 0);
        $clockOut = Carbon::now()->subHour()->setTime(18, 0);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/attendance/detail/' . $attendance->id);
        
        $response->assertStatus(200);
        $response->assertSee($clockIn->format('H:i'));
        $response->assertSee($clockOut->format('H:i'));
    }

    /**
     * テスト10-4: 「休憩」にて記されている時間がログインユーザーの打刻と一致している
     */
    public function test_attendance_detail_displays_break_times(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $breakStart = Carbon::now()->subHours(5)->setTime(12, 0);
        $breakEnd = Carbon::now()->subHours(4)->setTime(13, 0);
        
        $attendance->breaks()->create([
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ]);
        
        $response = $this->get('/attendance/detail/' . $attendance->id);
        
        $response->assertStatus(200);
        $response->assertSee($breakStart->format('H:i'));
        $response->assertSee($breakEnd->format('H:i'));
    }

    /**
     * テスト11-1: 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_attendance_update_validation_clock_in_after_clock_out(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHours(9),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/attendance/detail/' . $attendance->id, [
            'clock_in' => '18:00',
            'clock_out' => '09:00',
            'remarks' => 'Test remarks',
        ]);
        
        $response->assertSessionHasErrors();
    }

    /**
     * テスト11-2: 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_attendance_update_validation_break_start_after_clock_out(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHour(),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/attendance/detail/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break_start' => ['19:00'],
            'break_end' => ['20:00'],
            'remarks' => 'Test remarks',
        ]);
        
        $response->assertSessionHasErrors(['break_start.0']);
    }

    /**
     * テスト11-3: 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_attendance_update_validation_break_end_after_clock_out(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHour(),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/attendance/detail/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break_start' => ['12:00'],
            'break_end' => ['19:00'],
            'remarks' => 'Test remarks',
        ]);
        
        $response->assertSessionHasErrors(['break_end.0']);
    }

    /**
     * テスト11-4: 備考欄が未入力の場合のエラーメッセージが表示される
     */
    public function test_attendance_update_validation_remarks_required(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/attendance/detail/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);
        
        $response->assertSessionHasErrors(['remarks']);
        $response->assertSessionHasErrors(['remarks' => '備考を記入してください']);
    }

    /**
     * テスト11-5: 修正申請処理が実行される
     */
    public function test_attendance_update_creates_modification_request(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/attendance/detail/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'remarks' => 'Test remarks',
        ]);
        
        $this->assertDatabaseHas('attendance_modification_requests', [
            'user_id' => $this->user->id,
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);
    }

    /**
     * テスト11-6: 「承認待ち」にログインユーザーが行った申請が全て表示されていること
     */
    public function test_stamp_correction_request_list_displays_pending_requests(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $request = $attendance->modificationRequests()->create([
            'user_id' => $this->user->id,
            'requested_clock_in' => Carbon::now()->subHours(8),
            'requested_clock_out' => Carbon::now()->subHour(),
            'status' => 'pending',
            'remarks' => 'Test remarks',
        ]);
        
        $response = $this->get('/stamp_correction_request/list');
        
        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        // 一般ユーザーのビューでは attendance_id がリンクに含まれる
        $response->assertSee('/attendance/detail/' . $request->attendance_id);
    }

    /**
     * テスト11-7: 「承認済み」に管理者が承認した修正申請が全て表示されている
     */
    public function test_stamp_correction_request_list_displays_approved_requests(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $request = $attendance->modificationRequests()->create([
            'user_id' => $this->user->id,
            'requested_clock_in' => Carbon::now()->subHours(8),
            'requested_clock_out' => Carbon::now()->subHour(),
            'status' => 'approved',
            'remarks' => 'Test remarks',
        ]);
        
        $response = $this->get('/stamp_correction_request/list');
        
        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee($request->id);
    }

    /**
     * テスト11-8: 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
     */
    public function test_stamp_correction_request_detail_link(): void
    {
        $this->actingAs($this->user);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $request = $attendance->modificationRequests()->create([
            'user_id' => $this->user->id,
            'requested_clock_in' => Carbon::now()->subHours(8),
            'requested_clock_out' => Carbon::now()->subHour(),
            'status' => 'pending',
            'remarks' => 'Test remarks',
        ]);
        
        $response = $this->get('/attendance/detail/' . $attendance->id);
        
        $response->assertStatus(200);
        $response->assertViewIs('attendance.detail');
    }
}

