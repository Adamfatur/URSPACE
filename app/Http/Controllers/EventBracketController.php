<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\SpaceEvent;
use App\Models\EventBracket;
use App\Models\EventBracketParticipant;
use App\Models\EventBracketMatch;
use Illuminate\Http\Request;

class EventBracketController extends Controller
{
    public function store(Request $request, Space $space, SpaceEvent $event)
    {
        // Only event creator can create brackets
        if (auth()->id() !== $event->created_by) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'max_participants' => 'required|integer|min:4|max:64',
        ]);

        // Validate max_participants is power of 2
        $maxP = $request->max_participants;
        if (($maxP & ($maxP - 1)) !== 0) {
            return back()->withErrors(['max_participants' => 'Jumlah peserta harus kelipatan 2 (4, 8, 16, 32, 64)']);
        }

        $event->brackets()->create([
            'title' => $request->title,
            'description' => $request->description,
            'max_participants' => $request->max_participants,
            'status' => 'registration',
        ]);

        return back()->with('success', 'Bracket berhasil dibuat!');
    }

    public function addParticipant(Request $request, Space $space, SpaceEvent $event, EventBracket $bracket)
    {
        // Only event creator can add participants
        if (auth()->id() !== $event->created_by) {
            abort(403);
        }

        // Check if bracket is in registration phase
        if ($bracket->status !== 'registration') {
            return back()->with('error', 'Bracket sudah dimulai.');
        }

        // Check max participants
        if ($bracket->participants()->count() >= $bracket->max_participants) {
            return back()->with('error', 'Jumlah peserta sudah maksimal.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $bracket->participants()->create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'seed' => $bracket->participants()->count() + 1,
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function generateMatches(Request $request, Space $space, SpaceEvent $event, EventBracket $bracket)
    {
        // Only event creator can generate matches
        if (auth()->id() !== $event->created_by) {
            abort(403);
        }

        // Must have at least 2 participants
        $participants = $bracket->participants()->orderBy('seed')->get();
        if ($participants->count() < 2) {
            return back()->with('error', 'Minimal 2 peserta untuk memulai bracket.');
        }

        // Clear existing matches
        $bracket->matches()->delete();

        // Pad participants to power of 2 if needed
        $count = $participants->count();
        $bracketSize = 1;
        while ($bracketSize < $count) {
            $bracketSize *= 2;
        }

        // Calculate number of rounds
        $rounds = (int) log($bracketSize, 2);

        // Generate first round matches
        $matchOrder = 1;
        for ($i = 0; $i < $bracketSize / 2; $i++) {
            $p1 = $participants[$i] ?? null;
            $p2 = $participants[$bracketSize - 1 - $i] ?? null;

            $match = $bracket->matches()->create([
                'round' => 1,
                'match_order' => $matchOrder++,
                'participant_1_id' => $p1?->id,
                'participant_2_id' => $p2?->id,
            ]);

            // Auto-advance if bye (one participant null)
            if ($p1 && !$p2) {
                $match->update(['winner_id' => $p1->id]);
            } elseif ($p2 && !$p1) {
                $match->update(['winner_id' => $p2->id]);
            }
        }

        // Generate placeholder matches for subsequent rounds
        $matchesInRound = $bracketSize / 4;
        for ($round = 2; $round <= $rounds; $round++) {
            for ($i = 0; $i < $matchesInRound; $i++) {
                $bracket->matches()->create([
                    'round' => $round,
                    'match_order' => $i + 1,
                ]);
            }
            $matchesInRound = $matchesInRound / 2;
        }

        $bracket->update(['status' => 'ongoing']);

        return back()->with('success', 'Bracket berhasil di-generate!');
    }

    public function updateMatchResult(Request $request, Space $space, SpaceEvent $event, EventBracket $bracket, EventBracketMatch $match)
    {
        // Only event creator can update results
        if (auth()->id() !== $event->created_by) {
            abort(403);
        }

        $request->validate([
            'winner_id' => 'required|exists:event_bracket_participants,id',
            'score_1' => 'nullable|string|max:20',
            'score_2' => 'nullable|string|max:20',
        ]);

        // Validate winner is one of the participants
        if (!in_array($request->winner_id, [$match->participant_1_id, $match->participant_2_id])) {
            return back()->with('error', 'Pemenang harus salah satu peserta.');
        }

        $match->update([
            'winner_id' => $request->winner_id,
            'score_1' => $request->score_1,
            'score_2' => $request->score_2,
        ]);

        // Advance winner to next round
        $this->advanceWinner($bracket, $match);

        // Check if tournament is complete
        $totalRounds = (int) log($bracket->max_participants, 2);
        $finalMatch = $bracket->matches()->where('round', $totalRounds)->first();
        if ($finalMatch && $finalMatch->winner_id) {
            $bracket->update(['status' => 'completed']);
        }

        return back()->with('success', 'Hasil pertandingan berhasil diperbarui!');
    }

    private function advanceWinner(EventBracket $bracket, EventBracketMatch $match)
    {
        // Find the next round match
        $nextRound = $match->round + 1;
        $nextMatchOrder = (int) ceil($match->match_order / 2);

        $nextMatch = $bracket->matches()
            ->where('round', $nextRound)
            ->where('match_order', $nextMatchOrder)
            ->first();

        if (!$nextMatch) {
            return; // No next match (this was the final)
        }

        // Determine if winner goes to participant_1 or participant_2 slot
        if ($match->match_order % 2 === 1) {
            // Odd match order -> participant_1
            $nextMatch->update(['participant_1_id' => $match->winner_id]);
        } else {
            // Even match order -> participant_2
            $nextMatch->update(['participant_2_id' => $match->winner_id]);
        }
    }
}
