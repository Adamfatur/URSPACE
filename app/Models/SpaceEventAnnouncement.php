<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceEventAnnouncement extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'content',
    ];

    public function event()
    {
        return $this->belongsTo(SpaceEvent::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
