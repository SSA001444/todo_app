<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
