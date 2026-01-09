<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventVote extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'event_id',
        'uuid',
        'title',
        'description',
        'is_anonymous',
        'is_active',
        'ends_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_active' => 'boolean',
        'ends_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(SpaceEvent::class, 'event_id');
    }

    public function options()
    {
        return $this->hasMany(EventVoteOption::class, 'event_vote_id');
    }

    public function responses()
    {
        return $this->hasMany(EventVoteResponse::class, 'event_vote_id');
    }

    public function hasUserVoted($userId): bool
    {
        return $this->responses()->where('user_id', $userId)->exists();
    }

    public function getUserVote($userId)
    {
        return $this->responses()->where('user_id', $userId)->first();
    }
}
