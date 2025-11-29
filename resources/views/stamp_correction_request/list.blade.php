@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', '申請一覧')

@section('content')
<h1 class="page-title">申請一覧</h1>

<div class="tabs">
    <a href="?status=pending" class="tab {{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
    <a href="?status=approved" class="tab {{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
</div>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request->status_label }}</td>
                    <td>{{ auth()->user()->isAdmin() ? $request->user->name : auth()->user()->name }}</td>
                    <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
                    <td>{{ $request->requested_remarks ?: '遅延のため' }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        @if(auth()->user()->isAdmin())
                            <a href="/admin/stamp_correction_request/approve/{{ $request->id }}" class="btn btn-white">詳細</a>
                        @else
                            <a href="/attendance/detail/{{ $request->attendance_id }}" class="btn btn-white">詳細</a>
                        @endif
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

