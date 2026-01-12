<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Add this

class Notification extends Model
{
    use HasFactory, HasUuids; // Add HasUuids

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Notification types
    const TYPE_LIKE = 'like';
    const TYPE_COMMENT = 'comment';
    const TYPE_FOLLOW = 'follow';
    const TYPE_MENTION = 'mention';
    const TYPE_ANNOUNCEMENT = 'announcement';
    const TYPE_SPACE_JOIN = 'space_join';
    const TYPE_SPACE_EVENT = 'space_event';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRecent($query, $limit = 20)
    {
        return $query->latest()->limit($limit);
    }
}
