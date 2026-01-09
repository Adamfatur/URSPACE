<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['uuid', 'user_id', 'category_id', 'space_id', 'event_id', 'title', 'content', 'image', 'video_url', 'type', 'format', 'is_public', 'status', 'is_pinned', 'pinned_at', 'ai_moderation_score', 'ai_moderation_flags', 'ai_suggested_tags', 'ai_flagged', 'ai_moderated_at'];

    protected $casts = [
        'ai_moderation_flags' => 'array',
        'ai_suggested_tags' => 'array',
        'ai_flagged' => 'boolean',
        'ai_moderated_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            if (empty($thread->uuid)) {
                $thread->uuid = \Str::uuid();
            }
        });

        // Shadow Ban Global Scope
        // Hides threads from shadow-banned users unless:
        // 1. The viewer is the thread author (they see their own content)
        // 2. The viewer is an admin
        static::addGlobalScope('hideShadowBanned', function ($query) {
            if (auth()->check()) {
                $user = auth()->user();

                // Admins see everything
                if ($user->role === 'admin') {
                    return;
                }

                // For regular users: show their own threads + non-shadow-banned users' threads
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id) // Own content
                        ->orWhereHas('user', function ($uq) {
                            $uq->whereNull('shadow_banned_until')
                                ->orWhere('shadow_banned_until', '<=', now());
                        });
                });
            } else {
                // Guests: hide all shadow-banned content
                $query->whereHas('user', function ($q) {
                    $q->whereNull('shadow_banned_until')
                        ->orWhere('shadow_banned_until', '<=', now());
                });
            }
        });
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function event()
    {
        return $this->belongsTo(SpaceEvent::class, 'event_id');
    }

    public function pollOptions()
    {
        return $this->hasMany(PollOption::class);
    }

    public function userVoted($userId)
    {
        return PollVote::where('user_id', $userId)
            ->whereIn('poll_option_id', $this->pollOptions->pluck('id'))
            ->exists();
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function pinnedPost()
    {
        return $this->hasOne(Post::class)->where('is_pinned', true)->whereNull('parent_id');
    }

    public function previewPosts()
    {
        return $this->hasMany(Post::class)->whereNull('parent_id')->latest()->limit(2);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    public function views()
    {
        return $this->hasMany(ThreadView::class);
    }
    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        $disk = (string) env('THREAD_MEDIA_DISK', config('filesystems.default'));
        if (!in_array($disk, ['s3', 'public'], true)) {
            $disk = 's3';
        }

        try {
            return \Illuminate\Support\Facades\Storage::disk($disk)->url($this->image);
        } catch (\Throwable $e) {
            // If URL generation fails (misconfig), avoid breaking page render.
            return null;
        }
    }
}
