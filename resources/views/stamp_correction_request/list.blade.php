@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', '申請一覧')

@section('content')
<div style="padding: 3rem 14rem;">
    <h1 class="page-title">申請一覧</h1>

    <div class="tabs" style="border-bottom: 3px solid #999;">
        <a href="?status=pending" class="tab {{ $status === 'pending' ? 'active' : '' }}" style="padding: 1rem 2rem;">承認待ち</a>
        <a href="?status=approved" class="tab {{ $status === 'approved' ? 'active' : '' }}" style="padding: 1rem 2rem;">承認済み</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center; background-color: #ffffff;">状態</th>
                    <th style="text-align: left; background-color: #ffffff;">名前</th>
                    <th style="text-align: center; background-color: #ffffff;">対象日時</th>
                    <th style="text-align: left; background-color: #ffffff;">申請理由</th>
                    <th style="text-align: center; background-color: #ffffff;">申請日時</th>
                    <th style="text-align: center; background-color: #ffffff;">詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td style="text-align: center;">{{ $request->status_label }}</td>
                        <td style="text-align: left;">{{ auth()->user()->isAdmin() ? $request->user->name : auth()->user()->name }}</td>
                        <td style="text-align: center;">{{ $request->attendance->date->format('Y/m/d') }}</td>
                        <td style="text-align: left;">{{ $request->requested_remarks ?: '遅延のため' }}</td>
                        <td style="text-align: center;">{{ $request->created_at->format('Y/m/d') }}</td>
                        <td style="text-align: center;">
                            @if(auth()->user()->isAdmin())
                                <a href="/admin/stamp_correction_request/approve/{{ $request->id }}" class="btn btn-white" style="border: none;">詳細</a>
                            @else
                                <a href="/attendance/detail/{{ $request->attendance_id }}" class="btn btn-white" style="border: none;">詳細</a>
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
</div>
@endsection

