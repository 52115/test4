<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function build()
    {
        $verificationUrl = url('/email/verify/' . $this->user->id . '/' . sha1($this->user->email));

        return $this->subject('メールアドレスの確認')
            ->view('emails.verify')
            ->with([
                'user' => $this->user,
                'verificationUrl' => $verificationUrl,
            ]);
    }
}

