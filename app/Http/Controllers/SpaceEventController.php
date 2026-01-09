<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\SpaceEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpaceEventController extends Controller
{
    public function index(Space $space)
    {
        return redirect()->route('spaces.show', ['space' => $space->slug, 'tab' => 'events']);
    }

    public function store(Request $request, Space $space)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'visibility' => 'required|in:all_members,invited,open',
            'cover_image' => 'nullable|image|max:2048',
            'location_type' => 'nullable|in:online,offline,hybrid',
            'location_detail' => 'nullable|string|max:500',
        ]);

        $imagePath = null;
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('space-events', 'public');
        }

        $space->events()->create([
            'created_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'visibility' => $request->visibility,
            'cover_image' => $imagePath,
            'location_type' => $request->location_type ?? 'offline',
            'location_detail' => $request->location_detail,
        ]);

        return redirect()->route('spaces.show', ['space' => $space->slug, 'tab' => 'events'])->with('success', 'Acara berhasil dibuat!');
    }

    public function show(Space $space, SpaceEvent $event)
    {
        $event->load(['attendees.user', 'creator', 'announcements.user', 'votes.options.responses', 'votes.responses', 'brackets.participants', 'brackets.matches']);

        if (request()->ajax()) {
            return view('spaces.partials.event_details_content', compact('space', 'event'))->render();
        }

        return view('spaces.events.show', compact('space', 'event'));
    }

    public function update(Request $request, Space $space, SpaceEvent $event)
    {
        // Only event creator can update
        if (auth()->id() !== $event->created_by) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'visibility' => 'required|in:all_members,invited,open',
            'cover_image' => 'nullable|image|max:2048',
            'location_type' => 'nullable|in:online,offline,hybrid',
            'location_detail' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $imagePath = $request->file('cover_image')->store('space-events', 'public');
            $event->cover_image = $imagePath;
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'visibility' => $request->visibility,
            'location_type' => $request->location_type ?? 'offline',
            'location_detail' => $request->location_detail,
        ]);

        return redirect()->route('spaces.events.show', ['space' => $space->slug, 'event' => $event->uuid])
            ->with('success', 'Acara berhasil diperbarui!');
    }

    public function rsvp(Request $request, Space $space, SpaceEvent $event)
    {
        $request->validate([
            'status' => 'required|in:going,maybe,not_going',
        ]);

        $event->attendees()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['status' => $request->status]
        );

        if ($request->ajax()) {
            $event->load(['attendees.user', 'creator', 'announcements.user', 'votes.options.responses', 'votes.responses', 'brackets.participants', 'brackets.matches']);
            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran diperbarui.',
                'html' => view('spaces.partials.event_details_content', compact('space', 'event'))->render()
            ]);
        }

        return back()->with('success', 'Status kehadiran diperbarui.');
    }

    public function storeAnnouncement(Request $request, Space $space, SpaceEvent $event)
    {
        // Only event creator can post announcements
        if (auth()->id() !== $event->created_by) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $event->announcements()->create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Update acara berhasil dikirim!');
    }

    public function share(Request $request, Space $space, SpaceEvent $event)
    {
        // Check if space is private
        if ($space->is_private) {
            return response()->json([
                'success' => false,
                'message' => 'Acara dari grup private tidak dapat dibagikan ke timeline umum.'
            ], 403);
        }

        // Check if event is private (not 'open')
        if ($event->visibility !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya acara publik (terbuka) yang dapat dibagikan ke timeline.'
            ], 403);
        }

        // Check if user is member/owner as requested ("semua anggota dan owner")
        $user = auth()->user();
        $isMember = $space->members()->where('user_id', $user->id)->exists();
        $isOwner = $space->owner_id === $user->id;

        if (!$isMember && !$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya anggota grup yang dapat membagikan acara ini.'
            ], 403);
        }

        // Create a thread to share the event
        \App\Models\Thread::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'title' => 'Membagikan acara: ' . $event->title,
            'content' => 'Ayo ikuti acara ini di ' . $space->name . '!',
            'type' => 'text', // Standard text thread
            'format' => 'thread',
            'is_public' => true,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Acara berhasil dibagikan ke timeline Anda!'
        ]);
    }
}
