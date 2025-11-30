<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AttendanceModificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StampCorrectionRequestController extends Controller
{
    public function list(Request $request): View
    {
        $user = Auth::user();
        $status = $request->get('status', 'pending');

        if ($user->isAdmin()) {
            $requests = AttendanceModificationRequest::with(['user', 'attendance'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $requests = AttendanceModificationRequest::with(['attendance'])
                ->where('user_id', $user->id)
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('stamp_correction_request.list', compact('requests', 'status'));
    }
}

