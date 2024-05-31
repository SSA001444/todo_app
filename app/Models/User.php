<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    const ROLE_USER = 'user';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_ADMIN = 'admin';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'username',
        'email',
        'password',
        'team_id',
    ];

    public function todo()
    {
        return $this->BelongsToMany(Todo::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    protected $casts = [
      'email_verified_at' => 'datetime',
    ];

    public function hasVerifiedEmail()
    {
        return $this->is_email_verified == 1;
    }

}
