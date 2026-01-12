<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\SpaceJoinRequest;
use Illuminate\Http\Request;

class SpaceJoinRequestController extends Controller
{
    /**
     * User requests to join a private space.
     */
    public function store(Space $space)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if already a member
        if ($space->members->contains(auth()->id())) {
            return redirect()->back()->with('error', 'Kamu sudah menjadi anggota URSpace ini.');
        }

        // Check for existing pending request
        $existingRequest = $space->joinRequests()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'Kamu sudah mengajukan permintaan bergabung sebelumnya.');
        }

        SpaceJoinRequest::create([
            'space_id' => $space->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Permintaan bergabung telah dikirim ke pemilik URSpace.');
    }

    /**
     * User cancels their pending request.
     */
    public function destroy(Space $space)
    {
        $space->joinRequests()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->delete();

        return redirect()->back()->with('success', 'Permintaan bergabung dibatalkan.');
    }

    /**
     * Space owner approves a join request.
     */
    public function approve(Space $space, SpaceJoinRequest $request)
    {
        if (!auth()->check() || !$space->isAdmin(auth()->user())) {
            abort(403);
        }

        $request->approve();

        return redirect()->back()->with('success', 'Anggota baru telah ditambahkan ke URSpace.');
    }

    /**
     * Space owner rejects a join request.
     */
    public function reject(Space $space, SpaceJoinRequest $request)
    {
        if (!auth()->check() || !$space->isAdmin(auth()->user())) {
            abort(403);
        }

        $request->reject();

        return redirect()->back()->with('success', 'Permintaan bergabung ditolak.');
    }
}
