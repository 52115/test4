<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
    }

    /**
     * テスト12-1: その日になされた全ユーザーの勤怠情報が正確に確認できる
     */
    public function test_admin_attendance_list_displays_all_users_attendances(): void
    {
        $this->actingAs($this->admin);
        
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Attendance::create([
            'user_id' => $user1->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        Attendance::create([
            'user_id' => $user2->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/admin/attendance/list');
        
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
    }

    /**
     * テスト12-2: 遷移した際に現在の日付が表示される
     */
    public function test_admin_attendance_list_displays_current_date(): void
    {
        $this->actingAs($this->admin);
        
        $response = $this->get('/admin/attendance/list');
        
        $response->assertStatus(200);
        $response->assertSee(Carbon::today()->format('Y/m/d'));
    }

    /**
     * テスト12-3: 「前日」を押下した時に前の日の勤怠情報が表示される
     */
    public function test_admin_attendance_list_previous_day(): void
    {
        $this->actingAs($this->admin);
        
        $yesterday = Carbon::yesterday();
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $yesterday,
            'clock_in' => $yesterday->copy()->setTime(9, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/admin/attendance/list?date=' . $yesterday->format('Y-m-d'));
        
        $response->assertStatus(200);
        $response->assertSee($yesterday->format('Y/m/d'));
    }

    /**
     * テスト12-4: 「翌日」を押下した時に次の日の勤怠情報が表示される
     */
    public function test_admin_attendance_list_next_day(): void
    {
        $this->actingAs($this->admin);
        
        $tomorrow = Carbon::tomorrow();
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $tomorrow,
            'clock_in' => $tomorrow->copy()->setTime(9, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/admin/attendance/list?date=' . $tomorrow->format('Y-m-d'));
        
        $response->assertStatus(200);
        $response->assertSee($tomorrow->format('Y/m/d'));
    }

    /**
     * テスト13-1: 勤怠詳細画面に表示されるデータが選択したものになっている
     */
    public function test_admin_attendance_detail_displays_correct_data(): void
    {
        $this->actingAs($this->admin);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/admin/attendance/' . $attendance->id);
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.attendance.show');
        $response->assertSee($this->user->name);
    }

    /**
     * テスト13-2: 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_admin_attendance_update_validation_clock_in_after_clock_out(): void
    {
        $this->actingAs($this->admin);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHours(9),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/admin/attendance/' . $attendance->id, [
            'clock_in' => '18:00',
            'clock_out' => '09:00',
            'remarks' => 'Test remarks',
        ]);
        
        $response->assertSessionHasErrors();
    }

    /**
     * テスト13-3: 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_admin_attendance_update_validation_break_start_after_clock_out(): void
    {
        $this->actingAs($this->admin);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHour(),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/admin/attendance/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break_start' => ['19:00'],
            'break_end' => ['20:00'],
            'remarks' => 'Test remarks',
        ]);
        
        $response->assertSessionHasErrors(['break_start.0']);
    }

    /**
     * テスト13-4: 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     */
    public function test_admin_attendance_update_validation_break_end_after_clock_out(): void
    {
        $this->actingAs($this->admin);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHour(),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/admin/attendance/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break_start' => ['12:00'],
            'break_end' => ['19:00'],
            'remarks' => 'Test remarks',
        ]);
        
        $response->assertSessionHasErrors(['break_end.0']);
    }

    /**
     * テスト13-5: 備考欄が未入力の場合のエラーメッセージが表示される
     */
    public function test_admin_attendance_update_validation_remarks_required(): void
    {
        $this->actingAs($this->admin);
        
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->post('/admin/attendance/' . $attendance->id, [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);
        
        $response->assertSessionHasErrors(['remarks']);
        $response->assertSessionHasErrors(['remarks' => '備考を記入してください']);
    }
}

