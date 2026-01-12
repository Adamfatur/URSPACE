<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_banned',
        'shadow_banned_until',
        'bio',
        'avatar',
        'two_factor_secret',
        // New Profile Fields
        'nim',
        'program_studi',
        'fakultas',
        'angkatan',
        'headline',
        'location',
        'website',
        'linkedin_url',
        'github_url',
        'is_open_to_work',
        'open_to_work_types',
        'profile_completed_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_open_to_work' => 'boolean',
        'open_to_work_types' => 'array',
        'profile_completed_at' => 'datetime',
        'shadow_banned_until' => 'datetime',
    ];

    /**
     * Check if user is currently shadow banned.
     */
    public function isShadowBanned(): bool
    {
        return $this->shadow_banned_until && $this->shadow_banned_until->isFuture();
    }

    // Relationships
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }

    public function experiences()
    {
        return $this->hasMany(UserExperience::class)->orderByDesc('is_current')->orderByDesc('start_date');
    }

    public function educations()
    {
        return $this->hasMany(UserEducation::class)->orderByDesc('is_current')->orderByDesc('start_year');
    }

    public function skills()
    {
        return $this->hasMany(UserSkill::class);
    }

    public function certifications()
    {
        return $this->hasMany(UserCertification::class)->orderByDesc('issue_date');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            // Generate avatar using UI Avatars API based on user's name
            $name = urlencode($this->name ?? $this->username ?? 'User');
            // Use a consistent color based on user ID for personalization
            $colors = ['5e8b5e', '4a7c59', '3d6b4f', '6b8e6b', '7a9f7a', '8eb38e', '4e7d4e', '5a8a5a'];
            $colorIndex = $this->id ? ($this->id % count($colors)) : 0;
            $bgColor = $colors[$colorIndex];
            
            return "https://ui-avatars.com/api/?name={$name}&background={$bgColor}&color=ffffff&size=128&bold=true&format=svg";
        }

        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        // Explicitly use S3 disk
        return \Illuminate\Support\Facades\Storage::disk('s3')->url($this->avatar);
    }

    // Helper Methods
    public function isProfileComplete(): bool
    {
        return !empty($this->nim) && !empty($this->program_studi) && !empty($this->angkatan);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}

