<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function todo()
    {
        return $this->hasMany(Todo::class, 'group_id');
    }
    public function user()
    {
        return $this->belongsTo(User::Class);
    }
}
