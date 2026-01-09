<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalAnnouncement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of all announcements.
     */
    public function index()
    {
        $announcements = GlobalAnnouncement::with('user')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:1000',
            'type' => 'required|in:info,warning,danger,success',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|in:hours,days',
        ]);

        // Calculate expires_at based on duration
        $durationValue = (int) $validated['duration_value'];
        $durationUnit = $validated['duration_unit'];

        if ($durationUnit === 'hours') {
            $expiresAt = now()->addHours($durationValue);
        } else {
            // Limit to 30 days max
            $durationValue = min($durationValue, 30);
            $expiresAt = now()->addDays($durationValue);
        }

        GlobalAnnouncement::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'expires_at' => $expiresAt,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat!');
    }

    /**
     * Toggle the active status of an announcement.
     */
    public function toggle(GlobalAnnouncement $announcement)
    {
        $announcement->update([
            'is_active' => !$announcement->is_active,
        ]);

        $status = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.announcements.index')
            ->with('success', "Pengumuman berhasil {$status}!");
    }

    /**
     * Remove the specified announcement.
     */
    public function destroy(GlobalAnnouncement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus!');
    }
}
