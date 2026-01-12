<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Thread;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Get user's bookmarked threads.
     */
    public function index(Request $request)
    {
        $bookmarks = $request->user()
            ->bookmarks()
            ->with([
                'thread' => function ($query) {
                    $query->with(['user', 'category', 'space', 'likes', 'posts']);
                }
            ])
            ->latest()
            ->paginate(15);

        return view('bookmarks.index', compact('bookmarks'));
    }

    /**
     * Toggle bookmark on a thread.
     */
    public function toggle(Thread $thread)
    {
        $user = auth()->user();

        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
            $message = 'Thread dihapus dari simpanan';
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
            ]);
            $bookmarked = true;
            $message = 'Thread disimpan';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'bookmarked' => $bookmarked,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
