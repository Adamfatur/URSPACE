<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventVoteOption extends Model
{
    protected $fillable = [
        'event_vote_id',
        'option_text',
    ];

    public function vote()
    {
        return $this->belongsTo(EventVote::class, 'event_vote_id');
    }

    public function responses()
    {
        return $this->hasMany(EventVoteResponse::class, 'option_id');
    }

    public function responseCount(): int
    {
        return $this->responses()->count();
    }
}
