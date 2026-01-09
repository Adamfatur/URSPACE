<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display user's analytics dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Total thread views
        $totalViews = ThreadView::whereIn('thread_id', $user->threads()->pluck('id'))->count();

        // Views in last 7 days
        $recentViews = ThreadView::whereIn('thread_id', $user->threads()->pluck('id'))
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Top performing threads
        $topThreads = Thread::withCount('views')
            ->where('user_id', $user->id)
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        // Daily views for chart (last 30 days)
        $dailyViews = ThreadView::whereIn('thread_id', $user->threads()->pluck('id'))
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Total likes received
        $totalLikes = $user->threads()->withCount('likes')->get()->sum('likes_count')
            + $user->posts()->withCount('likes')->get()->sum('likes_count');

        // Total comments received
        $totalComments = $user->threads()
            ->withCount('posts')
            ->get()
            ->sum('posts_count');

        // Follower growth (placeholder - would need to track over time)
        $followerCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        return view('analytics.index', compact(
            'totalViews',
            'recentViews',
            'topThreads',
            'dailyViews',
            'totalLikes',
            'totalComments',
            'followerCount',
            'followingCount'
        ));
    }

    /**
     * Record a thread view.
     */
    public function recordView(Thread $thread, Request $request)
    {
        // Prevent duplicate views from same user/IP in short time
        $existingView = ThreadView::where('thread_id', $thread->id)
            ->where(function ($query) use ($request) {
                $query->where('ip_address', $request->ip())
                    ->orWhere('user_id', $request->user()?->id);
            })
            ->where('created_at', '>=', now()->subMinutes(30))
            ->exists();

        if (!$existingView) {
            ThreadView::create([
                'thread_id' => $thread->id,
                'user_id' => $request->user()?->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
    }
}
