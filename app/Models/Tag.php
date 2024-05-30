<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'team_id'];

    protected $hidden = ['team_id'];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'tag_ticket');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}