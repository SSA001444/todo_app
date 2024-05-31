<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        $locale = App::getLocale();
        $view = 'email.verification.' . $locale . '.verificationEmail';

        return $this->view($view)
            ->subject(__('messages.email_verification'))
            ->with([
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}
