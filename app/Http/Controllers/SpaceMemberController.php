<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Space;
use App\Models\User;
use App\Models\SpaceBan;
use App\Models\SpaceReport;

class SpaceMemberController extends Controller
{
    public function join(Space $space)
    {
        // Check if user is banned
        if ($space->isBanned(auth()->user())) {
            return back()->with('error', 'Anda telah di-ban dari URSpace ini.');
        }

        if (!$space->members->contains(auth()->id())) {
            $space->members()->attach(auth()->id(), ['role' => 'member']);
            return back()->with('success', 'Berhasil bergabung ke ruang!');
        }
        return back()->with('info', 'Anda sudah menjadi anggota ruang ini.');
    }

    public function leave(Space $space)
    {
        if ($space->members->contains(auth()->id())) {
            // Prevent owner from leaving without transferring ownership
            if ($space->owner_id == auth()->id()) {
                return back()->with('error', 'Pemilik tidak dapat keluar dari ruang. Hapus ruang jika ingin menutupnya.');
            }

            $space->members()->detach(auth()->id());
            return back()->with('success', 'Berhasil keluar dari ruang.');
        }
        return back()->with('error', 'Anda bukan anggota ruang ini.');
    }

    public function addMember(Request $request, Space $space)
    {
        if (!$space->isAdmin(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'identifier' => 'required|string',
            'role' => 'required|in:admin,moderator,member',
        ]);

        $user = User::where('username', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        if (!$user) {
            return back()->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($space->members->contains($user->id)) {
            return back()->with('info', 'Pengguna sudah menjadi anggota ruang ini.');
        }

        $space->members()->attach($user->id, ['role' => $request->role]);

        return back()->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function updateRole(Request $request, Space $space, User $user)
    {
        if (!$space->isAdmin(auth()->user())) {
            abort(403);
        }

        // Prevent changing owner's role
        if ($user->id === $space->owner_id) {
            return back()->with('error', 'Peran pemilik tidak dapat diubah.');
        }

        $request->validate([
            'role' => 'required|in:admin,moderator,member',
        ]);

        $space->members()->updateExistingPivot($user->id, ['role' => $request->role]);

        return back()->with('success', 'Peran anggota berhasil diperbarui!');
    }

    public function removeMember(Space $space, User $user)
    {
        if (!$space->isAdmin(auth()->user())) {
            abort(403);
        }

        if ($user->id === $space->owner_id) {
            return back()->with('error', 'Pemilik tidak dapat dihapus dari ruang.');
        }

        $space->members()->detach($user->id);

        return back()->with('success', 'Anggota berhasil dihapus dari ruang.');
    }

    /**
     * Ban a member from the space (Admin only)
     */
    public function ban(Request $request, Space $space, User $user)
    {
        if (!auth()->check() || !$space->isAdmin(auth()->user())) {
            abort(403);
        }

        // Prevent banning owner
        if ($space->owner_id === $user->id) {
            return back()->with('error', 'Tidak dapat mem-ban pemilik space.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Remove from members
        $space->members()->detach($user->id);

        // Add to ban list
        SpaceBan::updateOrCreate(
            ['space_id' => $space->id, 'user_id' => $user->id],
            ['banned_by' => auth()->id(), 'reason' => $request->reason]
        );

        return back()->with('success', "{$user->name} telah di-ban dari URSpace ini.");
    }

    /**
     * Unban a user (Admin only)
     */
    public function unban(Request $request, Space $space, User $user)
    {
        if (!auth()->check() || !$space->isAdmin(auth()->user())) {
            abort(403);
        }

        SpaceBan::where('space_id', $space->id)->where('user_id', $user->id)->delete();

        return back()->with('success', "{$user->name} telah di-unban.");
    }

    /**
     * Report a user to global admin (Moderator+)
     */
    public function report(Request $request, Space $space, User $user)
    {
        if (!auth()->check() || !$space->canModerate(auth()->user())) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        SpaceReport::create([
            'space_id' => $space->id,
            'reported_user_id' => $user->id,
            'reporter_id' => auth()->id(),
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Laporan berhasil dikirim ke Admin Global.');
    }
}
