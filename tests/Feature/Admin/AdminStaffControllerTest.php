<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffControllerTest extends TestCase
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
     * テスト14-1: 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
     */
    public function test_admin_staff_list_displays_all_users(): void
    {
        $this->actingAs($this->admin);
        
        $user1 = User::factory()->create(['name' => 'User 1', 'email' => 'user1@example.com']);
        $user2 = User::factory()->create(['name' => 'User 2', 'email' => 'user2@example.com']);
        
        $response = $this->get('/admin/staff/list');
        
        $response->assertStatus(200);
        $response->assertSee('User 1');
        $response->assertSee('user1@example.com');
        $response->assertSee('User 2');
        $response->assertSee('user2@example.com');
    }

    /**
     * テスト14-2: ユーザーの勤怠情報が正しく表示される
     */
    public function test_admin_staff_monthly_attendance_displays_correctly(): void
    {
        $this->actingAs($this->admin);
        
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/admin/attendance/staff/' . $this->user->id);
        
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee(Carbon::today()->format('Y'));
    }

    /**
     * テスト14-3: 「前月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_admin_staff_monthly_attendance_previous_month(): void
    {
        $this->actingAs($this->admin);
        
        $lastMonth = Carbon::now()->subMonth();
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $lastMonth->copy()->startOfMonth(),
            'clock_in' => $lastMonth->copy()->startOfMonth()->setTime(9, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/admin/attendance/staff/' . $this->user->id . '?year=' . $lastMonth->year . '&month=' . $lastMonth->month);
        
        $response->assertStatus(200);
        $response->assertSee($lastMonth->format('Y'));
        $response->assertSee(str_pad($lastMonth->month, 2, '0', STR_PAD_LEFT));
    }

    /**
     * テスト14-4: 「翌月」を押下した時に表示月の翌月の情報が表示される
     */
    public function test_admin_staff_monthly_attendance_next_month(): void
    {
        $this->actingAs($this->admin);
        
        $nextMonth = Carbon::now()->addMonth();
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $nextMonth->copy()->startOfMonth(),
            'clock_in' => $nextMonth->copy()->startOfMonth()->setTime(9, 0),
            'status' => 'clocked_out',
        ]);
        
        $response = $this->get('/admin/attendance/staff/' . $this->user->id . '?year=' . $nextMonth->year . '&month=' . $nextMonth->month);
        
        $response->assertStatus(200);
        $response->assertSee($nextMonth->format('Y'));
        $response->assertSee(str_pad($nextMonth->month, 2, '0', STR_PAD_LEFT));
    }

    /**
     * テスト14-5: 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
     */
    public function test_admin_staff_monthly_attendance_detail_link(): void
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
    }
}

