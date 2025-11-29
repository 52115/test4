@extends('layouts.admin')

@section('title', 'スタッフ別勤怠一覧')

@section('content')
<h1 class="page-title">■ {{ $user->name }}さんの勤怠</h1>

<div class="month-nav">
    <a href="?year={{ $prevYear }}&month={{ $prevMonth }}">←前月</a>
    <span class="month-display">📅 {{ $year }}/{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}</span>
    <a href="?year={{ $nextYear }}&month={{ $nextMonth }}">翌月→</a>
</div>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @php
                $daysInMonth = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
            @endphp
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $attendance = $attendances->first(function($att) use ($date) {
                        return $att->date->format('Y-m-d') === $date;
                    });
                    $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'][date('w', strtotime($date))];
                @endphp
                <tr>
                    <td>{{ sprintf('%02d/%02d(%s)', $month, $day, $dayOfWeek) }}</td>
                    <td>{{ $attendance && $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}</td>
                    <td>{{ $attendance && $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}</td>
                    <td>
                        @if($attendance && $attendance->breaks->count() > 0)
                            @php
                                $totalBreakMinutes = $attendance->breaks->sum(function($breakTime) {
                                    if ($breakTime->break_start && $breakTime->break_end) {
                                        return $breakTime->break_start->diffInMinutes($breakTime->break_end);
                                    }
                                    return 0;
                                });
                                $breakHours = floor($totalBreakMinutes / 60);
                                $breakMins = $totalBreakMinutes % 60;
                            @endphp
                            {{ sprintf('%d:%02d', $breakHours, $breakMins) }}
                        @endif
                    </td>
                    <td>
                        @if($attendance && $attendance->clock_in && $attendance->clock_out)
                            @php
                                $totalMinutes = $attendance->clock_in->diffInMinutes($attendance->clock_out);
                                $totalBreakMinutes = $attendance->breaks->sum(function($breakTime) {
                                    if ($breakTime->break_start && $breakTime->break_end) {
                                        return $breakTime->break_start->diffInMinutes($breakTime->break_end);
                                    }
                                    return 0;
                                });
                                $totalMinutes -= $totalBreakMinutes;
                                $totalHours = floor($totalMinutes / 60);
                                $totalMins = $totalMinutes % 60;
                            @endphp
                            {{ sprintf('%d:%02d', $totalHours, $totalMins) }}
                        @endif
                    </td>
                    <td>
                        @if($attendance)
                            <a href="/admin/attendance/{{ $attendance->id }}" class="btn btn-white">詳細</a>
                        @else
                            <span class="btn btn-white" style="opacity: 0.5;">詳細</span>
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

<div style="text-align: right; margin-top: 2rem;">
    <a href="/admin/attendance/staff/{{ $user->id }}/export?year={{ $year }}&month={{ $month }}" class="btn btn-black">CSV出力</a>
</div>
@endsection

