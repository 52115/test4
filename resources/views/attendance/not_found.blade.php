@extends('layouts.app')

@section('title', '勤怠詳細')

@section('content')
<div style="padding: 3rem 14rem;">
    <h1 class="page-title">勤怠詳細</h1>

    <div class="card" style="text-align: center; padding: 3rem;">
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">勤怠登録がされていません</p>
        <div>
            <a href="/attendance/list" class="btn btn-black">戻る</a>
        </div>
    </div>
</div>
@endsection

