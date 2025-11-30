<?php

namespace App\Providers;

use App\Listeners\SendCustomEmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendCustomEmailVerificationNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

