<?php

namespace App\Mail;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShareTodo extends Mailable
{
    use Queueable, SerializesModels;

    public $todo;

    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    public function build()
    {
        return $this->view('email.shareTodo')
            ->subject('Notification about new shared todo');
    }
}
