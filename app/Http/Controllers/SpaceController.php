<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Space;

class SpaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Space::withCount(['members', 'threads']);

        // Filter by tab
        $tab = $request->get('tab', 'discover');

        if ($tab === 'my-spaces' && auth()->check()) {
            // Show user's spaces (including pending ones they own)
            $query->whereHas('members', function ($q) {
                $q->where('user_id', auth()->id());
            });
        } else {
            // Public listing: Only show approved spaces
            $query->where('status', 'approved');
        }

        // Sorting
        if ($request->get('sort') === 'trending') {
            $query->orderByRaw('(members_count + threads_count) DESC');
        } elseif ($request->get('sort') === 'latest') {
            $query->orderBy('last_activity_at', 'desc')->latest();
        } else {
            $query->latest();
        }

        $spaces = $query->get();

        return view('spaces.index', compact('spaces', 'tab'));
    }

    public function create()
    {
        return view('spaces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:spaces,name',
            'description' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
            'is_private' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('spaces', 'public');
        }

        $space = Space::create([
            'name' => $request->name,
            'description' => $request->description,
            'cover_image' => $imagePath,
            'owner_id' => auth()->id(),
            'is_private' => $request->boolean('is_private'),
            'status' => 'pending', // All new spaces require approval
        ]);

        // Auto-join owner as admin
        $space->members()->attach(auth()->id(), ['role' => 'admin']);

        return redirect()->route('spaces.show', $space)->with('success', 'URSpace berhasil dibuat dan sedang menunggu persetujuan admin (maks 1x24 jam).');
    }

    public function show(Space $space, Request $request)
    {
        // Block access to non-approved spaces for non-owners
        if (!$space->isApproved() && (!auth()->check() || $space->owner_id !== auth()->id())) {
            abort(404);
        }

        $space->load(['owner', 'members']);
        $tab = $request->get('tab', 'discussions');

        // Initialize variables
        $threads = collect();
        $members = collect();
        $joinRequests = collect();
        $pinnedThreads = collect();
        $announcements = collect();
        $upcomingEvents = collect();
        $pastEvents = collect();
        $mediaItems = collect();

        $isMember = auth()->check() && $space->members->contains(auth()->id());
        $isAdmin = auth()->check() && $space->isAdmin(auth()->user());
        $isModerator = auth()->check() && $space->isModerator(auth()->user());

        if ($isMember || !$space->is_private) {
            // Fetch Pinned Threads (Always load, but display only in discussions tab)
            $pinnedThreads = $space->pinnedThreads()->with(['user', 'category', 'likes', 'posts', 'pollOptions'])->get();

            // Fetch Announcements
            $announcements = $space->activeAnnouncements()->get();

            if ($tab === 'members') {
                $members = $space->members()->paginate(20);
                $joinRequests = $space->joinRequests()->with('user')->where('status', 'pending')->get();
            } elseif ($tab === 'events') {
                $upcomingEvents = $space->events()->where('starts_at', '>', now())->orderBy('starts_at')->get();
                $pastEvents = $space->events()->where('starts_at', '<=', now())->orderByDesc('starts_at')->paginate(10);
            } elseif ($tab === 'media') {
                $mediaItems = $space->threads()->whereNotNull('image')->orderByDesc('created_at')->paginate(24);
            } else {
                // Discussions Tab Logic
                $query = $space->threads()
                    ->where('is_pinned', false) // Exclude pinned from main list
                    ->with(['user', 'category', 'likes', 'posts', 'pollOptions']);

                if (request('sort') === 'trending') {
                    $query->orderByRaw('(likes_count + posts_count) DESC');
                } else {
                    $query->latest();
                }

                $threads = $query->paginate(10);
            }
        }

        // Check if user has a pending join request
        $hasPendingRequest = false;
        if (auth()->check() && !$isMember) {
            $hasPendingRequest = $space->joinRequests()
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->exists();
        }

        return view('spaces.show', compact(
            'space',
            'threads',
            'members',
            'joinRequests',
            'isMember',
            'isAdmin',
            'isModerator',
            'hasPendingRequest',
            'tab',
            'announcements',
            'pinnedThreads',
            'upcomingEvents',
            'pastEvents',
            'mediaItems'
        ));
    }

    public function pinThread(Request $request, Space $space, \App\Models\Thread $thread)
    {
        if (!auth()->check() || !$space->canModerate(auth()->user())) {
            abort(403);
        }

        if ($thread->space_id !== $space->id) {
            abort(404);
        }

        $isPinned = !$thread->is_pinned;
        $thread->update([
            'is_pinned' => $isPinned,
            'pinned_at' => $isPinned ? now() : null,
        ]);

        return back()->with('success', $isPinned ? 'Thread berhasil di-pin!' : 'Pin thread dilepas.');
    }

    public function update(Request $request, Space $space)
    {
        if (!auth()->check() || !$space->isAdmin(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:spaces,name,' . $space->id,
            'description' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('spaces', 'public');
        }

        $space->update($data);

        return redirect()->route('spaces.show', $space)->with('success', 'Pengaturan URSpace berhasil disimpan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
