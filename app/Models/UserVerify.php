<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'users_verify';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
