<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventVoteResponse extends Model
{
    protected $fillable = [
        'event_vote_id',
        'option_id',
        'user_id',
    ];

    public function vote()
    {
        return $this->belongsTo(EventVote::class, 'event_vote_id');
    }

    public function option()
    {
        return $this->belongsTo(EventVoteOption::class, 'option_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
