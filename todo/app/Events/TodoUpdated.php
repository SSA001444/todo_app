<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Todo;

class TodoUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $todo;

    public function __construct(Todo $todo)
    {
    $this->todo = $todo;
    }

    public function broadcastAs()
    {
    return 'TodoUpdated';
    }

    public function broadcastOn()
    {
     return new PrivateChannel('todos.' . $this->todo->id);
    }
}
