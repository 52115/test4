<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function show(): View
    {
        return view('auth.verify-email');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/attendance');
        }

        Mail::to($request->user())->send(new VerifyEmail($request->user()));

        return back()->with('status', '認証メールを再送信しました');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect('/attendance');
        }

        if (sha1($user->email) === $hash) {
            if ($user->markEmailAsVerified()) {
                event(new \Illuminate\Auth\Events\Verified($user));
            }
        }

        return redirect('/attendance');
    }
}

