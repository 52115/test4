@extends('layouts.app')

@section('title', '勤怠詳細')

@section('content')
<div style="padding: 3rem 14rem;">
    <h1 class="page-title">勤怠詳細</h1>

    <form action="/attendance/detail/{{ $attendance->id }}" method="POST" novalidate>
        @csrf
    <div class="card">
        <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">名前</label>
            <span style="flex: 1;">{{ $attendance->user->name }}</span>
        </div>

        <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">日付</label>
            <span style="flex: 1;">{{ $attendance->date->format('Y年') }} {{ $attendance->date->format('n月j日') }}</span>
        </div>

        <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">出勤・退勤</label>
            <div style="flex: 1;">
                @if($hasPendingRequest)
                    <span>{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }} ~ {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}</span>
                @else
                    <div class="time-input" style="display: flex; align-items: center;">
                        <input type="time" name="clock_in" value="{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}" style="border: 1px solid #E5E5E5; border-radius: 4px; padding: 0.5rem; text-align: center;">
                        <span>~</span>
                        <input type="time" name="clock_out" value="{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}" style="border: 1px solid #E5E5E5; border-radius: 4px; padding: 0.5rem; text-align: center;">
                    </div>
                    @error('clock_in')
                        <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    @error('clock_out')
                        <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                @endif
            </div>
        </div>

        @php
            $breakCount = $attendance->breaks->count();
            $maxBreaks = max(2, $breakCount + 1); // 最低2つ、既存の数+1まで
        @endphp
        @for($i = 0; $i < $maxBreaks; $i++)
            <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
                <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</label>
                <div style="flex: 1;">
                    @if($hasPendingRequest)
                        @if(isset($attendance->breaks[$i]) && $attendance->breaks[$i]->break_start && $attendance->breaks[$i]->break_end)
                            <span>{{ $attendance->breaks[$i]->break_start->format('H:i') }} ~ {{ $attendance->breaks[$i]->break_end->format('H:i') }}</span>
                        @endif
                    @else
                        <div class="time-input">
                            @php
                                $breakStart = isset($attendance->breaks[$i]) && $attendance->breaks[$i]->break_start ? $attendance->breaks[$i]->break_start->format('H:i') : null;
                                $breakEnd = isset($attendance->breaks[$i]) && $attendance->breaks[$i]->break_end ? $attendance->breaks[$i]->break_end->format('H:i') : null;
                            @endphp
                            <input type="time" name="break_start[]" @if($breakStart) value="{{ $breakStart }}" @endif style="border: 1px solid #E5E5E5; border-radius: 4px; padding: 0.5rem; text-align: center;">
                            <span>~</span>
                            <input type="time" name="break_end[]" @if($breakEnd) value="{{ $breakEnd }}" @endif style="border: 1px solid #E5E5E5; border-radius: 4px; padding: 0.5rem; text-align: center;">
                        </div>
                        @error('break_start.' . $i)
                            <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                        @error('break_end.' . $i)
                            <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
            </div>
        @endfor

        <div style="display: flex; padding: 1rem 0;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">備考</label>
            <div style="flex: 1;">
                @if($hasPendingRequest)
                    <span style="text-align: right; display: block;">{{ $attendance->remarks }}</span>
                @else
                    <textarea name="remarks" rows="4" style="width: 100%; padding: 0.5rem; border: 1px solid #E5E5E5; border-radius: 4px; font-size: 1rem;">@if($errors->has('remarks'))@else{{ old('remarks', $attendance->remarks) }}@endif</textarea>
                    @error('remarks')
                        <div style="color: #ff0000; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                @endif
            </div>
        </div>
    </div>

    @if(!$hasPendingRequest)
        <div style="text-align: right; margin-top: 2rem;">
            <button type="submit" class="btn btn-black">修正</button>
        </div>
    @endif
    </form>

    @if($hasPendingRequest)
        <div style="text-align: right; margin-top: 1rem; color: #ff0000; font-style: italic;">
            *承認待ちのため修正はできません。
        </div>
    @endif
</div>
@endsection

