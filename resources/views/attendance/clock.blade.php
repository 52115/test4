@extends('layouts.app')

@section('title', '勤怠登録')

@section('content')
<div class="clock-container">
    @php
        $statusLabels = [
            'off_duty' => '勤務外',
            'working' => '出勤中',
            'on_break' => '休憩中',
            'clocked_out' => '退勤済'
        ];
        $statusLabel = $statusLabels[$status] ?? '勤務外';
    @endphp

    <div class="status-badge">{{ $statusLabel }}</div>
    <div class="clock-date">{{ $currentDate }}</div>
    <div class="clock-time">{{ $currentTime }}</div>

    @if($status === 'off_duty')
        <form action="/attendance/clock-in" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-black" style="padding: 1rem 3rem; font-size: 1.25rem;">出勤</button>
        </form>
    @elseif($status === 'working')
        <div class="btn-group" style="gap: 2rem;">
            <form action="/attendance/clock-out" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-black" style="padding: 1rem 3rem; font-size: 1.25rem; min-width: 150px;">退勤</button>
            </form>
            <form action="/attendance/break-start" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-white" style="padding: 1rem 3rem; font-size: 1.25rem; min-width: 150px;">休憩入</button>
            </form>
        </div>
    @elseif($status === 'on_break')
        <form action="/attendance/break-end" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-black" style="padding: 1rem 3rem; font-size: 1.25rem;">休憩戻</button>
        </form>
    @elseif($status === 'clocked_out')
        <p style="font-size: 1.25rem; margin-top: 2rem;">お疲れ様でした。</p>
    @endif
</div>
@endsection

