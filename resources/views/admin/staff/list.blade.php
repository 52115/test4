@extends('layouts.admin')

@section('title', 'スタッフ一覧')

@section('content')
<h1 class="page-title">スタッフ一覧</h1>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staff as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="/admin/attendance/staff/{{ $user->id }}" class="btn btn-white">詳細</a>
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
@endsection

