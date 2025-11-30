@extends('layouts.admin')

@section('title', '修正申請承認')

@section('content')
<div style="padding: 3rem 14rem;">
    <h1 class="page-title">勤怠詳細</h1>

    <div class="card">
        <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">名前</label>
            <span style="flex: 1;">{{ $modificationRequest->attendance->user->name }}</span>
        </div>

        <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">日付</label>
            <span style="flex: 1;">{{ $modificationRequest->attendance->date->format('Y年') }} {{ $modificationRequest->attendance->date->format('n月j日') }}</span>
        </div>

        <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">出勤・退勤</label>
            <span style="flex: 1;">{{ $modificationRequest->requested_clock_in ? $modificationRequest->requested_clock_in->format('H:i') : '' }} ~ {{ $modificationRequest->requested_clock_out ? $modificationRequest->requested_clock_out->format('H:i') : '' }}</span>
        </div>

        @php
            $breakMods = $modificationRequest->breakModifications;
            $maxBreaks = max(2, $breakMods->count()); // 最低2つ、既存の数まで
        @endphp
        @for($i = 0; $i < $maxBreaks; $i++)
            <div style="display: flex; padding: 1rem 0; border-bottom: 1px solid #E5E5E5;">
                <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</label>
                <div style="flex: 1;">
                    @if(isset($breakMods[$i]) && $breakMods[$i]->requested_break_start && $breakMods[$i]->requested_break_end)
                        <span>{{ $breakMods[$i]->requested_break_start->format('H:i') }} ~ {{ $breakMods[$i]->requested_break_end->format('H:i') }}</span>
                    @endif
                </div>
            </div>
        @endfor

        <div style="display: flex; padding: 1rem 0;">
            <label style="font-weight: 600; width: 250px; flex-shrink: 0; margin-right: 3rem;">備考</label>
            <span style="flex: 1;">{{ $modificationRequest->requested_remarks }}</span>
        </div>
    </div>

    <form action="/admin/stamp_correction_request/approve/{{ $modificationRequest->id }}" method="POST" style="text-align: right; margin-top: 2rem;" id="approve-form">
        @csrf
        <button type="submit" class="btn btn-black" id="approve-button" style="{{ $modificationRequest->status === 'approved' ? 'background-color: #999;' : '' }}" {{ $modificationRequest->status === 'approved' ? 'disabled' : '' }}>
            {{ $modificationRequest->status === 'approved' ? '承認済み' : '承認' }}
        </button>
    </form>
    
    <script>
        // フォーム送信後にボタンのテキストを「承認済み」に変更
        document.getElementById('approve-form').addEventListener('submit', function(e) {
            const button = document.getElementById('approve-button');
            if (button && !button.disabled) {
                setTimeout(function() {
                    button.textContent = '承認済み';
                    button.disabled = true;
                    button.style.backgroundColor = '#999';
                }, 100);
            }
        });
    </script>
</div>
@endsection

