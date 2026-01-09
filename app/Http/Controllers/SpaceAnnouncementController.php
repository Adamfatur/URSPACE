<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\SpaceAnnouncement;
use Illuminate\Http\Request;

class SpaceAnnouncementController extends Controller
{
    public function store(Request $request, Space $space)
    {
        if (!auth()->check() || !$space->canModerate(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $space->announcements()->create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'expires_at' => $request->expires_at,
        ]);

        return back()->with('success', 'Pengumuman berhasil dibuat!');
    }

    public function destroy(Space $space, SpaceAnnouncement $announcement)
    {
        if (!auth()->check() || !$space->canModerate(auth()->user())) {
            abort(403);
        }

        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus!');
    }
}
