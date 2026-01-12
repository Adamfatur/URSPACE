<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBracketMatch extends Model
{
    protected $fillable = [
        'bracket_id',
        'round',
        'match_order',
        'participant_1_id',
        'participant_2_id',
        'winner_id',
        'score_1',
        'score_2',
    ];

    public function bracket()
    {
        return $this->belongsTo(EventBracket::class, 'bracket_id');
    }

    public function participant1()
    {
        return $this->belongsTo(EventBracketParticipant::class, 'participant_1_id');
    }

    public function participant2()
    {
        return $this->belongsTo(EventBracketParticipant::class, 'participant_2_id');
    }

    public function winner()
    {
        return $this->belongsTo(EventBracketParticipant::class, 'winner_id');
    }
}
