<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\AttendanceModificationRequest;
use App\Models\BreakModification;
use App\Models\BreakTime as BreakModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function list(Request $request): View
    {
        $user = Auth::user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->get();

        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        return view('attendance.list', compact('attendances', 'year', 'month', 'prevYear', 'prevMonth', 'nextYear', 'nextMonth'));
    }

    public function detail(Request $request, int $id): View
    {
        $user = Auth::user();
        
        // 勤怠登録がされていない日付の場合
        if ($id === 0 && $request->has('date')) {
            $date = $request->get('date');
            return view('attendance.not_found', compact('date'));
        }
        
        $attendance = Attendance::with(['breaks', 'modificationRequests'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $hasPendingRequest = $attendance->modificationRequests()
            ->where('status', 'pending')
            ->exists();

        return view('attendance.detail', compact('attendance', 'hasPendingRequest'));
    }

    public function update(UpdateAttendanceRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $attendance = Attendance::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $hasPendingRequest = $attendance->modificationRequests()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return back();
        }

        $modificationRequest = AttendanceModificationRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => $request->input('clock_in') ? now()->setTimeFromTimeString($request->input('clock_in')) : null,
            'requested_clock_out' => $request->input('clock_out') ? now()->setTimeFromTimeString($request->input('clock_out')) : null,
            'requested_remarks' => $request->input('remarks'),
            'status' => 'pending',
        ]);

        $breakStarts = $request->input('break_start', []);
        $breakEnds = $request->input('break_end', []);

        foreach ($breakStarts as $index => $breakStart) {
            if ($breakStart && isset($breakEnds[$index]) && $breakEnds[$index]) {
                BreakModification::create([
                    'attendance_modification_request_id' => $modificationRequest->id,
                    'requested_break_start' => now()->setTimeFromTimeString($breakStart),
                    'requested_break_end' => now()->setTimeFromTimeString($breakEnds[$index]),
                ]);
            }
        }

        return redirect('/attendance/list');
    }
}

