<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Space;
use Illuminate\Http\Request;

class SpaceApprovalController extends Controller
{
    public function index()
    {
        $pendingSpaces = Space::where('status', 'pending')
            ->with('owner')
            ->withCount('members')
            ->latest()
            ->paginate(20);

        return view('admin.spaces.pending', compact('pendingSpaces'));
    }

    public function approve(Space $space)
    {
        $space->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // TODO: Notify owner via notification system

        return redirect()->back()->with('success', "URSpace '{$space->name}' telah disetujui!");
    }

    public function reject(Request $request, Space $space)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $space->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // TODO: Notify owner via notification system

        return redirect()->back()->with('success', "URSpace '{$space->name}' telah ditolak.");
    }
}
