<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalAnnouncement extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active and non-expired announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    /**
     * Get the admin user who created the announcement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the announcement has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
