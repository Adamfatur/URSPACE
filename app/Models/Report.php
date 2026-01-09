<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['reporter_id', 'reported_id', 'reported_type', 'reason', 'status', 'ai_priority_score', 'ai_suggested_action', 'ai_analysis', 'ai_confidence', 'ai_analyzed_at'];

    protected $casts = [
        'ai_analyzed_at' => 'datetime',
    ];

    public function reported()
    {
        return $this->morphTo();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
