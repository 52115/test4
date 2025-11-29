@extends('layouts.app')

@section('title', '勤怠詳細')

@section('content')
<h1 class="page-title">勤怠詳細</h1>

@if($hasPendingRequest)
    <div class="alert alert-error" style="color: #ff0000;">
        *承認待ちのため修正はできません。
    </div>
@endif

<div class="card">
    <form action="/attendance/detail/{{ $attendance->id }}" method="POST">
        @csrf
        <div style="margin-bottom: 1.5rem;">
            <label style="display: inline-block; width: 150px; font-weight: 600;">名前</label>
            <span>{{ $attendance->user->name }}</span>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: inline-block; width: 150px; font-weight: 600;">日付</label>
            <span>{{ $attendance->date->format('Y年 n月j日') }}</span>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: inline-block; width: 150px; font-weight: 600;">出勤・退勤</label>
            <div class="time-input">
                <input type="time" name="clock_in" value="{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                <span>~</span>
                <input type="time" name="clock_out" value="{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: inline-block; width: 150px; font-weight: 600;">休憩</label>
            <div>
                @foreach($attendance->breaks as $index => $breakTime)
                    <div class="time-input" style="margin-bottom: 0.5rem;">
                        <input type="time" name="break_start[]" value="{{ $breakTime->break_start ? $breakTime->break_start->format('H:i') : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        <span>~</span>
                        <input type="time" name="break_end[]" value="{{ $breakTime->break_end ? $breakTime->break_end->format('H:i') : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                    </div>
                @endforeach
                <div class="time-input">
                    <input type="time" name="break_start[]" value="" {{ $hasPendingRequest ? 'disabled' : '' }}>
                    <span>~</span>
                    <input type="time" name="break_end[]" value="" {{ $hasPendingRequest ? 'disabled' : '' }}>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: inline-block; width: 150px; font-weight: 600;">備考</label>
            <textarea name="remarks" rows="4" style="width: 100%; padding: 0.5rem; border: 1px solid #000000; border-radius: 4px;" {{ $hasPendingRequest ? 'disabled' : '' }}>{{ $attendance->remarks }}</textarea>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="list-style: none; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$hasPendingRequest)
            <div style="text-align: right; margin-top: 2rem;">
                <button type="submit" class="btn btn-black">修正</button>
            </div>
        @endif
    </form>
</div>
@endsection

