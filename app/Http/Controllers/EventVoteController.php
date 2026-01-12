<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\SpaceEvent;
use App\Models\EventVote;
use App\Models\EventVoteOption;
use Illuminate\Http\Request;

class EventVoteController extends Controller
{
    public function store(Request $request, Space $space, SpaceEvent $event)
    {
        // Only event creator can create votes
        if (auth()->id() !== $event->created_by) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_anonymous' => 'nullable|boolean',
            'ends_at' => 'nullable|date',
            'options' => 'required|array|min:2|max:10',
            'options.*' => 'required|string|max:255',
        ]);

        $vote = $event->votes()->create([
            'title' => $request->title,
            'description' => $request->description,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'ends_at' => $request->ends_at,
        ]);

        foreach ($request->options as $optionText) {
            $vote->options()->create(['option_text' => $optionText]);
        }

        return back()->with('success', 'Voting berhasil dibuat!');
    }

    public function castVote(Request $request, Space $space, SpaceEvent $event, EventVote $vote)
    {
        // Check if already voted
        if ($vote->hasUserVoted(auth()->id())) {
            return back()->with('error', 'Anda sudah memberikan suara.');
        }

        // Check if vote has ended
        if ($vote->ends_at && $vote->ends_at->isPast()) {
            return back()->with('error', 'Voting sudah berakhir.');
        }

        // Check if vote is active
        if (!$vote->is_active) {
            return back()->with('error', 'Voting tidak aktif.');
        }

        $request->validate([
            'option_id' => 'required|exists:event_vote_options,id',
        ]);

        // Verify option belongs to this vote
        $option = EventVoteOption::where('id', $request->option_id)
            ->where('event_vote_id', $vote->id)
            ->firstOrFail();

        $vote->responses()->create([
            'option_id' => $request->option_id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Suara Anda telah tercatat!');
    }
}
