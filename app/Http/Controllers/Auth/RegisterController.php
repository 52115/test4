<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
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

        Auth::login($user);

        return redirect('/attendance');
    }
}

