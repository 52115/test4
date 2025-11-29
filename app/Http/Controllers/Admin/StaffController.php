<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function list(): View
    {
        $staff = User::where('role', 'user')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.staff.list', compact('staff'));
    }

    public function monthlyAttendance(Request $request, int $id): View
    {
        $user = User::findOrFail($id);
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

        return view('admin.attendance.staff', compact('user', 'attendances', 'year', 'month', 'prevYear', 'prevMonth', 'nextYear', 'nextMonth'));
    }

    public function exportCsv(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();

        $filename = "{$user->name}_{$year}_{$month}_attendance.csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['日付', '出勤', '退勤', '休憩', '合計', '備考']);
            
            foreach ($attendances as $attendance) {
                $totalBreakMinutes = $attendance->breaks->sum(function($break) {
                    if ($break->break_start && $break->break_end) {
                        return $break->break_start->diffInMinutes($break->break_end);
                    }
                    return 0;
                });
                
                $totalMinutes = 0;
                if ($attendance->clock_in && $attendance->clock_out) {
                    $totalMinutes = $attendance->clock_in->diffInMinutes($attendance->clock_out) - $totalBreakMinutes;
                }
                
                $totalHours = floor($totalMinutes / 60);
                $totalMins = $totalMinutes % 60;
                $totalTime = sprintf('%d:%02d', $totalHours, $totalMins);
                
                $breakTime = sprintf('%d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60);
                
                fputcsv($file, [
                    $attendance->date->format('Y/m/d'),
                    $attendance->clock_in ? $attendance->clock_in->format('H:i') : '',
                    $attendance->clock_out ? $attendance->clock_out->format('H:i') : '',
                    $breakTime,
                    $totalTime,
                    $attendance->remarks ?? '',
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

