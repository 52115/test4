<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
        $this->app->singleton(ResetsUserPasswords::class, ResetUserPassword::class);
    }
}

