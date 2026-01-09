<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBracketParticipant extends Model
{
    protected $fillable = [
        'bracket_id',
        'user_id',
        'name',
        'seed',
    ];

    public function bracket()
    {
        return $this->belongsTo(EventBracket::class, 'bracket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get display name (use custom name or user's name)
    public function getDisplayNameAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }
        return $this->user ? $this->user->name : 'TBD';
    }
}
