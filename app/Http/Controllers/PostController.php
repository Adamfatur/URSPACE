<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request, Thread $thread)
    {
        // Anti-spam: Rate limiting for comment creation
        $user = $request->user();
        $minuteCount = $user->posts()->where('created_at', '>=', now()->subMinute())->count();
        $hourlyCount = $user->posts()->where('created_at', '>=', now()->subHour())->count();

        if ($minuteCount >= 10) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak komentar. Tunggu sebentar.'
                ], 429);
            }
            return back()->with('error', 'Terlalu banyak komentar dalam waktu singkat. Tunggu sebentar.');
        }

        if ($hourlyCount >= 100) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas komentar per jam tercapai. Coba lagi nanti.'
                ], 429);
            }
            return back()->with('error', 'Batas komentar per jam tercapai. Coba lagi nanti.');
        }

        $request->validate([
            'content' => 'required|string|max:256',
            'parent_id' => 'nullable|exists:posts,id'
        ]);

        $post = $thread->posts()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $request->parent_id,
            'content' => $request->input('content'),
        ]);

        // Record engagement for personalized timeline
        app(\App\Services\UserEngagementService::class)
            ->recordEngagement($request->user()->id, $thread, 'comment');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Balasan terkirim!',
                'post' => $post->load('user'),
                'posts_count' => $thread->posts()->count(),
            ]);
        }

        return back()->with('success', 'Balasan terkirim!');
    }

    public function destroy(Request $request, Post $post)
    {
        // Allow deletion if user owns the post OR owns the thread
        if ($request->user()->id !== $post->user_id && $request->user()->id !== $post->thread->user_id) {
            abort(403, 'Unauthorized');
        }

        $post->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Komentar dihapus.']);
        }

        return back()->with('success', 'Komentar dihapus.');
    }

    public function hide(Request $request, Post $post)
    {
        // Only thread owner can hide
        if ($request->user()->id !== $post->thread->user_id) {
            abort(403, 'Unauthorized');
        }

        $status = $post->status === 'hidden' ? 'active' : 'hidden';
        $post->update(['status' => $status]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $status === 'hidden' ? 'Komentar disembunyikan.' : 'Komentar ditampilkan kembali.',
                'status' => $status
            ]);
        }

        return back();
    }

    public function togglePin(Request $request, Post $post)
    {
        // Only thread owner can pin
        if ($request->user()->id !== $post->thread->user_id) {
            abort(403, 'Unauthorized');
        }

        if (!$post->is_pinned) {
            // Check max 3 pins
            $pinnedCount = $post->thread->posts()->where('is_pinned', true)->count();
            if ($pinnedCount >= 3) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maksimal 3 komentar yang dapat di-pin!'
                    ], 422);
                }
                return back()->with('error', 'Maksimal 3 komentar pinned.');
            }
        }

        $post->update(['is_pinned' => !$post->is_pinned]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $post->is_pinned ? 'Komentar di-pin.' : 'Pin dilepas.',
                'is_pinned' => $post->is_pinned
            ]);
        }

        return back();
    }
}
