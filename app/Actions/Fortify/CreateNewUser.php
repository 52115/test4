<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        // RegisterRequestで既にバリデーション済みのため、ここではバリデーションをスキップ
        // ただし、emailのuniqueチェックは必要
        if (isset($input['email']) && User::where('email', $input['email'])->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['このメールアドレスは既に登録されています'],
            ]);
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => 'user',
        ]);
    }
}

