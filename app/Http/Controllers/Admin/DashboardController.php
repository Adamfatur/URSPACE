<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'threads' => Thread::count(),
            'reports' => Report::where('status', 'pending')->count(),
            'posts' => \App\Models\Post::count(),
        ];

        // Simple data for charts (Last 7 days)
        $dates = collect(range(0, 6))->map(function ($day) {
            return now()->subDays($day)->format('Y-m-d');
        })->reverse()->values();

        $userRegistrations = $dates->map(function ($date) {
            return User::whereDate('created_at', $date)->count();
        });

        $threadCreation = $dates->map(function ($date) {
            return Thread::whereDate('created_at', $date)->count();
        });

        $recentUsers = User::latest()->take(5)->get();
        $recentThreads = Thread::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'dates', 'userRegistrations', 'threadCreation', 'recentUsers', 'recentThreads'));
    }
}
