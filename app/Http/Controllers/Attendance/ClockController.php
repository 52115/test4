<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BreakTime as BreakModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClockController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $status = $attendance ? $attendance->status : 'off_duty';
        $currentTime = now()->format('H:i');
        $currentDate = now()->format('Y年n月j日(') . ['日', '月', '火', '水', '木', '金', '土'][now()->dayOfWeek] . ')';

        return view('attendance.clock', compact('status', 'currentTime', 'currentDate', 'attendance'));
    }

    public function clockIn(): RedirectResponse
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->clock_in) {
            return back()->with('error', '本日は既に出勤しています');
        }

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'clock_in' => now(),
                'status' => 'working',
            ]);
        } else {
            $attendance->update([
                'clock_in' => now(),
                'status' => 'working',
            ]);
        }

        return redirect('/attendance')->with('success', '出勤しました');
    }

    public function breakStart(): RedirectResponse
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'working')
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        $attendance->update(['status' => 'on_break']);

        BreakModel::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);

        return redirect('/attendance')->with('success', '休憩を開始しました');
    }

    public function breakEnd(): RedirectResponse
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'on_break')
            ->first();

        if (!$attendance) {
            return back()->with('error', '休憩中ではありません');
        }

        $attendance->update(['status' => 'working']);

        $break = BreakModel::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest()
            ->first();

        if ($break) {
            $break->update(['break_end' => now()]);
        }

        return redirect('/attendance')->with('success', '休憩を終了しました');
    }

    public function clockOut(): RedirectResponse
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereIn('status', ['working', 'on_break'])
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        if ($attendance->status === 'on_break') {
            $break = BreakModel::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->latest()
                ->first();

            if ($break) {
                $break->update(['break_end' => now()]);
            }
        }

        $attendance->update([
            'clock_out' => now(),
            'status' => 'clocked_out',
        ]);

        return redirect('/attendance')->with('message', 'お疲れ様でした。');
    }
}

