<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\AttendanceModificationRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStampCorrectionRequestControllerTest extends TestCase
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
     * テスト15-1: 承認待ちの修正申請が全て表示されている
     */
    public function test_admin_stamp_correction_request_list_displays_pending_requests(): void
    {
        $this->actingAs($this->admin);
        
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'status' => 'clocked_out',
        ]);
        
        $request1 = $attendance1->modificationRequests()->create([
            'user_id' => $user1->id,
            'requested_clock_in' => Carbon::now()->subHours(8),
            'requested_clock_out' => Carbon::now()->subHour(),
            'status' => 'pending',
            'remarks' => 'Test remarks 1',
        ]);
        
        $request2 = $attendance2->modificationRequests()->create([
            'user_id' => $user2->id,
            'requested_clock_in' => Carbon::now()->subHours(8),
            'requested_clock_out' => Carbon::now()->subHour(),
            'status' => 'pending',
            'remarks' => 'Test remarks 2',
        ]);
        
        $response = $this->get('/admin/stamp_correction_request/list');
        
        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
    }

    /**
     * テスト15-2: 承認済みの修正申請が全て表示されている
     */
    public function test_admin_stamp_correction_request_list_displays_approved_requests(): void
    {
        $this->actingAs($this->admin);
        
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
            'requested_remarks' => 'Test remarks',
        ]);
        
        $response = $this->get('/admin/stamp_correction_request/list?status=approved');
        
        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee($this->user->name);
    }

    /**
     * テスト15-3: 修正申請の詳細内容が正しく表示されている
     */
    public function test_admin_stamp_correction_request_detail_displays_correctly(): void
    {
        $this->actingAs($this->admin);
        
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
            'requested_remarks' => 'Test remarks',
        ]);
        
        $response = $this->get('/admin/stamp_correction_request/approve/' . $request->id);
        
        $response->assertStatus(200);
        $response->assertViewIs('stamp_correction_request.approve');
        $response->assertSee($this->user->name);
        $response->assertSee('Test remarks');
    }

    /**
     * テスト15-4: 修正申請の承認処理が正しく行われる
     */
    public function test_admin_stamp_correction_request_approval(): void
    {
        $this->actingAs($this->admin);
        
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
        
        $response = $this->post('/admin/stamp_correction_request/approve/' . $request->id);
        
        $request->refresh();
        $this->assertEquals('approved', $request->status);
        
        $attendance->refresh();
        $this->assertNotNull($attendance->clock_out);
    }
}

