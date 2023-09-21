<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $guarded = [];

    public function todo()
    {
        return $this->hasMany(Todo::class);
    }

    protected $casts = [
      'email_verified_at' => 'datetime',
    ];
}
