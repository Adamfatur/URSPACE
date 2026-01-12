<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Display a list of all users for management.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name, username, or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by shadow ban status
        if ($request->filter === 'shadow_banned') {
            $query->where('shadow_banned_until', '>', now());
        }

        $users = $query->withCount(['threads', 'posts'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Apply shadow ban to a user.
     */
    public function shadowBan(Request $request, User $user)
    {
        $request->validate([
            'duration' => 'required|in:1,3,7,30,permanent',
        ]);

        if ($user->role === 'admin' || $user->role === 'global_admin') {
            return back()->with('error', 'Tidak dapat shadow ban admin.');
        }

        $duration = $request->duration;

        if ($duration === 'permanent') {
            $user->shadow_banned_until = now()->addYears(100);
        } else {
            $user->shadow_banned_until = now()->addDays((int) $duration);
        }

        $user->save();

        return back()->with('success', "User @{$user->username} telah di-shadow ban selama {$duration} hari.");
    }

    /**
     * Remove shadow ban from a user.
     */
    public function removeShadowBan(User $user)
    {
        $user->shadow_banned_until = null;
        $user->save();

        return back()->with('success', "Shadow ban untuk @{$user->username} telah dicabut.");
    }
}
