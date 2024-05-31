<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\App;

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
        $locale = App::getLocale();
        $view = 'email.currentEmailChange.' . $locale;

        return $this->view($view)
                    ->subject(__('messages.verify_current_email'))
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token,
                    ]);
    }
}
