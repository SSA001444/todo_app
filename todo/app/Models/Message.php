<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ['user_id', 'message', 'recipient_id', 'edited'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
