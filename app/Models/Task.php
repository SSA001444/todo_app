<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'name',
        'completed',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
