<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = ['thread_id', 'option_text', 'votes_count'];

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }
}
