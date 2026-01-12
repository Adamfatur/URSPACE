<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SpaceEvent extends Model
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
        'space_id',
        'uuid',
        'created_by',
        'title',
        'description',
        'cover_image',
        'starts_at',
        'ends_at',
        'visibility',
        'location_type',
        'location_detail',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendees()
    {
        return $this->hasMany(SpaceEventAttendee::class, 'event_id');
    }

    public function goingAttendees()
    {
        return $this->attendees()->where('status', 'going');
    }

    public function announcements()
    {
        return $this->hasMany(SpaceEventAnnouncement::class, 'event_id')->latest();
    }

    public function votes()
    {
        return $this->hasMany(EventVote::class, 'event_id');
    }

    public function brackets()
    {
        return $this->hasMany(EventBracket::class, 'event_id');
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at->isFuture();
    }

    public function isOngoing(): bool
    {
        return $this->starts_at->isPast() && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function isPast(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
