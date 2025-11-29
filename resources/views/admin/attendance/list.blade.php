@extends('layouts.admin')

@section('title', '日次勤怠一覧')

@section('content')
<h1 class="page-title">{{ date('Y年n月j日', strtotime($date)) }}の勤怠</h1>

<div class="month-nav">
    <a href="?date={{ $prevDate }}">←前日</a>
    <span class="month-display">📅 {{ date('Y/m/d', strtotime($date)) }}</span>
    <a href="?date={{ $nextDate }}">翌日→</a>
</div>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}</td>
                    <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}</td>
                    <td>
                        @if($attendance->breaks->count() > 0)
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
                        @if($attendance->clock_in && $attendance->clock_out)
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
                        <a href="/admin/attendance/{{ $attendance->id }}" class="btn btn-white">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">データがありません</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

