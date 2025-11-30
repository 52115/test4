@extends('layouts.admin')

@section('title', 'スタッフ一覧')

@section('content')
<div style="padding: 3rem 14rem;">
    <h1 class="page-title">スタッフ一覧</h1>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center; background-color: #ffffff;">名前</th>
                    <th style="text-align: center; background-color: #ffffff;">メールアドレス</th>
                    <th style="text-align: center; background-color: #ffffff;">月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff as $user)
                    <tr>
                        <td style="text-align: center; padding-left: 3rem;">{{ $user->name }}</td>
                        <td style="text-align: center;">{{ $user->email }}</td>
                        <td style="text-align: center;">
                            <a href="/admin/attendance/staff/{{ $user->id }}" class="btn btn-white" style="border: none;">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem;">データがありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

