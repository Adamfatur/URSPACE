<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceReport extends Model
{
    protected $fillable = [
        'space_id',
        'reported_user_id',
        'reporter_id',
        'reason',
        'status',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
