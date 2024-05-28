<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'new_email',
        'current_email_verification_token',
        'new_email_verification_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
