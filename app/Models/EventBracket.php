<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventBracket extends Model
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
        'max_participants',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(SpaceEvent::class, 'event_id');
    }

    public function participants()
    {
        return $this->hasMany(EventBracketParticipant::class, 'bracket_id');
    }

    public function matches()
    {
        return $this->hasMany(EventBracketMatch::class, 'bracket_id');
    }
}
