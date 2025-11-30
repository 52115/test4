<?php

namespace App\Listeners;

use App\Mail\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendCustomEmailVerificationNotification
{
    public function handle(Registered $event): void
    {
        Mail::to($event->user)->send(new VerifyEmail($event->user));
    }
}

