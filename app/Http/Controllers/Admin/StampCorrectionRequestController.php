<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceModificationRequest;
use App\Models\BreakTime as BreakModel;
use App\Models\BreakModification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StampCorrectionRequestController extends Controller
{
    public function approve(int $id): View
    {
        $modificationRequest = AttendanceModificationRequest::with([
            'attendance.user',
            'attendance.breaks',
            'breakModifications'
        ])->findOrFail($id);

        return view('stamp_correction_request.approve', compact('modificationRequest'));
    }

    public function approveRequest(int $id): RedirectResponse
    {
        $modificationRequest = AttendanceModificationRequest::with([
            'attendance',
            'breakModifications'
        ])->findOrFail($id);

        if ($modificationRequest->status !== 'pending') {
            return back()->with('error', 'この申請は既に処理されています');
        }

        $attendance = $modificationRequest->attendance;

        $attendance->update([
            'clock_in' => $modificationRequest->requested_clock_in,
            'clock_out' => $modificationRequest->requested_clock_out,
            'remarks' => $modificationRequest->requested_remarks,
        ]);

        BreakModel::where('attendance_id', $attendance->id)->delete();

        foreach ($modificationRequest->breakModifications as $breakMod) {
            BreakModel::create([
                'attendance_id' => $attendance->id,
                'break_start' => $breakMod->requested_break_start,
                'break_end' => $breakMod->requested_break_end,
            ]);
        }

        $modificationRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect('/stamp_correction_request/list?status=approved')
            ->with('success', '承認しました');
    }
}

