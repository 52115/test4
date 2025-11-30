<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\BreakTime as BreakModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function list(Request $request): View
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $attendances = Attendance::with(['user', 'breaks'])
            ->where('date', $date)
            ->orderBy('clock_in', 'asc')
            ->get();

        $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
        $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));

        return view('admin.attendance.list', compact('attendances', 'date', 'prevDate', 'nextDate'));
    }

    public function show(int $id): View
    {
        $attendance = Attendance::with(['user', 'breaks', 'modificationRequests'])
            ->findOrFail($id);

        $hasPendingRequest = $attendance->modificationRequests()
            ->where('status', 'pending')
            ->exists();

        return view('admin.attendance.show', compact('attendance', 'hasPendingRequest'));
    }

    public function update(UpdateAttendanceRequest $request, int $id): RedirectResponse
    {
        $attendance = Attendance::findOrFail($id);

        $hasPendingRequest = $attendance->modificationRequests()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return back();
        }

        $attendance->update([
            'clock_in' => $request->input('clock_in') ? now()->setTimeFromTimeString($request->input('clock_in')) : null,
            'clock_out' => $request->input('clock_out') ? now()->setTimeFromTimeString($request->input('clock_out')) : null,
            'remarks' => $request->input('remarks'),
        ]);

        BreakModel::where('attendance_id', $attendance->id)->delete();

        $breakStarts = $request->input('break_start', []);
        $breakEnds = $request->input('break_end', []);

        foreach ($breakStarts as $index => $breakStart) {
            if ($breakStart && isset($breakEnds[$index]) && $breakEnds[$index]) {
                BreakModel::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => now()->setTimeFromTimeString($breakStart),
                    'break_end' => now()->setTimeFromTimeString($breakEnds[$index]),
                ]);
            }
        }

        return redirect('/admin/attendance/list');
    }
}

