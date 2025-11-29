<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            if (!$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'ログイン情報が登録されていません',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended('/admin/attendance/list');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->onlyInput('email');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/admin/login');
    }
}

