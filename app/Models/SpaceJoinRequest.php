<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceJoinRequest extends Model
{
    protected $fillable = ['space_id', 'user_id', 'status', 'message'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);

        // Add user to space members
        $this->space->members()->attach($this->user_id, ['role' => 'member']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
