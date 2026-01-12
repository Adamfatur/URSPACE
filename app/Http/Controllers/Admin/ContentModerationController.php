<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Http\Request;

class ContentModerationController extends Controller
{
    /**
     * List all hidden threads.
     */
    public function hiddenIndex()
    {
        $hiddenThreads = Thread::with(['user', 'category'])
            ->where('status', 'hidden')
            ->latest()
            ->paginate(20);

        return view('admin.moderation.hidden', compact('hiddenThreads'));
    }

    /**
     * Toggle thread visibility (hide/show).
     */
    public function hideThread(Request $request, Thread $thread)
    {
        $newStatus = $thread->status === 'hidden' ? 'active' : 'hidden';
        $thread->update(['status' => $newStatus]);

        $message = $newStatus === 'hidden'
            ? 'Thread disembunyikan dari publik.'
            : 'Thread ditampilkan kembali.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $newStatus
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Permanently delete a thread (admin only).
     */
    public function destroyThread(Request $request, Thread $thread)
    {
        $thread->posts()->delete(); // Delete all comments
        $thread->pollOptions()->delete(); // Delete poll options
        $thread->tags()->detach(); // Detach tags
        $thread->likes()->delete(); // Delete likes
        $thread->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thread dihapus secara permanen.'
            ]);
        }

        return redirect()->route('home')->with('success', 'Thread dihapus secara permanen.');
    }
}
