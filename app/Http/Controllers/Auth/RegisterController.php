<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request, CreatesNewUsers $creator): RedirectResponse
    {
        $validated = $request->validated();
        // password_confirmationを削除
        unset($validated['password_confirmation']);
        
        $user = $creator->create($validated);

        // Registeredイベントを発火してメール認証メールを送信
        event(new Registered($user));

        Auth::login($user);

        // メール認証が完了していない場合は、メール認証誘導画面にリダイレクト
        if (!$user->hasVerifiedEmail()) {
            return redirect('/email/verify');
        }

        return redirect('/attendance');
    }
}

