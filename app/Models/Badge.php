<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'criteria_type',
        'criteria_value',
        'points',
    ];

    // Criteria types
    const CRITERIA_THREADS_COUNT = 'threads_count';
    const CRITERIA_POSTS_COUNT = 'posts_count';
    const CRITERIA_LIKES_RECEIVED = 'likes_received';
    const CRITERIA_FOLLOWERS_COUNT = 'followers_count';
    const CRITERIA_PROFILE_COMPLETE = 'profile_complete';
    const CRITERIA_DAYS_SINCE_JOINED = 'days_since_joined';

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Check if a user qualifies for this badge.
     */
    public function userQualifies(User $user): bool
    {
        $value = 0;

        switch ($this->criteria_type) {
            case self::CRITERIA_THREADS_COUNT:
                $value = $user->threads()->count();
                break;
            case self::CRITERIA_POSTS_COUNT:
                $value = $user->posts()->count();
                break;
            case self::CRITERIA_LIKES_RECEIVED:
                $value = $user->threads()->withCount('likes')->get()->sum('likes_count')
                    + $user->posts()->withCount('likes')->get()->sum('likes_count');
                break;
            case self::CRITERIA_FOLLOWERS_COUNT:
                $value = $user->followers()->count();
                break;
            case self::CRITERIA_PROFILE_COMPLETE:
                $value = $user->isProfileComplete() ? 1 : 0;
                break;
            case self::CRITERIA_DAYS_SINCE_JOINED:
                $value = $user->created_at->diffInDays(now());
                break;
        }

        return $value >= $this->criteria_value;
    }
}
