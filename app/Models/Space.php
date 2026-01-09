<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'cover_image',
        'owner_id',
        'is_private',
        'status',
        'approved_at',
        'rejected_at',
        'rejection_reason'
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($space) {
            $space->uuid = (string) \Illuminate\Support\Str::uuid();
            if (empty($space->slug)) {
                $space->slug = \Illuminate\Support\Str::slug($space->name);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // === Relationships ===

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'space_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(SpaceJoinRequest::class);
    }

    public function announcements()
    {
        return $this->hasMany(SpaceAnnouncement::class);
    }

    public function activeAnnouncements()
    {
        return $this->announcements()->active()->latest();
    }

    public function bans()
    {
        return $this->hasMany(SpaceBan::class);
    }

    public function events()
    {
        return $this->hasMany(SpaceEvent::class);
    }

    public function reports()
    {
        return $this->hasMany(SpaceReport::class);
    }

    // === Permission Helpers ===

    public function isAdmin(User $user): bool
    {
        return $this->owner_id === $user->id ||
            $this->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists();
    }

    public function isModerator(User $user): bool
    {
        return $this->isAdmin($user) ||
            $this->members()->where('user_id', $user->id)->wherePivot('role', 'moderator')->exists();
    }

    public function canModerate(User $user): bool
    {
        return $this->isModerator($user);
    }

    public function isBanned(User $user): bool
    {
        return $this->bans()->where('user_id', $user->id)->exists();
    }

    public function getMemberRole(User $user): ?string
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member ? $member->pivot->role : null;
    }

    // === Status Helpers ===

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // === Pinned Threads ===

    public function pinnedThreads()
    {
        return $this->threads()->where('is_pinned', true)->orderByDesc('pinned_at');
    }

    // === Media Gallery ===

    public function mediaItems()
    {
        return $this->threads()->whereNotNull('image')->orderByDesc('created_at');
    }
}
