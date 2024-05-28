<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class CurrentEmailChangeNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct(private readonly User $user, $token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('email.currentEmailChange')
            ->subject('Confirm Your Current Email Address')
            ->with([
                'user' => $this->user,
                'token' => $this->token,
            ]);
    }
}
