@extends('layouts.admin')

@section('title', '修正申請承認')

@section('content')
<h1 class="page-title">勤怠詳細</h1>

<div class="card">
    <div style="margin-bottom: 1.5rem;">
        <label style="display: inline-block; width: 150px; font-weight: 600;">名前</label>
        <span>{{ $modificationRequest->attendance->user->name }}</span>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <label style="display: inline-block; width: 150px; font-weight: 600;">日付</label>
        <span>{{ $modificationRequest->attendance->date->format('Y年 n月j日') }}</span>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <label style="display: inline-block; width: 150px; font-weight: 600;">出勤・退勤</label>
        <div>
            {{ $modificationRequest->requested_clock_in ? $modificationRequest->requested_clock_in->format('H:i') : '' }} ~ 
            {{ $modificationRequest->requested_clock_out ? $modificationRequest->requested_clock_out->format('H:i') : '' }}
        </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <label style="display: inline-block; width: 150px; font-weight: 600;">休憩</label>
        <div>
            @foreach($modificationRequest->breakModifications as $breakMod)
                <div style="margin-bottom: 0.5rem;">
                    {{ $breakMod->requested_break_start ? $breakMod->requested_break_start->format('H:i') : '' }} ~ 
                    {{ $breakMod->requested_break_end ? $breakMod->requested_break_end->format('H:i') : '' }}
                </div>
            @endforeach
        </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <label style="display: inline-block; width: 150px; font-weight: 600;">備考</label>
        <div>{{ $modificationRequest->requested_remarks }}</div>
    </div>

    <form action="/admin/stamp_correction_request/approve/{{ $modificationRequest->id }}" method="POST" style="text-align: right; margin-top: 2rem;">
        @csrf
        <button type="submit" class="btn btn-black">承認</button>
    </form>
</div>
@endsection

