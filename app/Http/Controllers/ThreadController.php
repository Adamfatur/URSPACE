<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ThreadController extends Controller
{
    private function threadMediaDisk(): string
    {
        // Keep thread media off the app server. Default to S3 (or whatever
        // FILESYSTEM_DISK is), but allow overriding via THREAD_MEDIA_DISK.
        $disk = (string) env('THREAD_MEDIA_DISK', config('filesystems.default'));

        // Only allow known disks for thread media.
        if (!in_array($disk, ['s3', 'public'], true)) {
            $disk = 's3';
        }

        if ($disk === 's3') {
            // Fail fast if S3 isn't configured; do NOT fallback to local storage.
            $key = (string) config('filesystems.disks.s3.key');
            $secret = (string) config('filesystems.disks.s3.secret');
            $bucket = (string) config('filesystems.disks.s3.bucket');
            $region = (string) config('filesystems.disks.s3.region');

            if ($key === '' || $secret === '' || $bucket === '' || $region === '') {
                Log::error('S3 is selected for thread media but is not configured', [
                    's3_key_set' => $key !== '',
                    's3_secret_set' => $secret !== '',
                    's3_bucket_set' => $bucket !== '',
                    's3_region_set' => $region !== '',
                ]);
            }
        }

        return $disk;
    }

    private function storeThreadMedia(UploadedFile $file, string $disk, string $directory): string
    {
        $directory = trim($directory, '/');
        $filename = $file->hashName();

        // Force throwing exceptions so we don't get silent `false` on failures.
        // This project configures disks with `throw=false`, and in this Laravel
        // version we can't call Storage::disk()->throw(), so we toggle config and
        // reset the disk instance.
        $throwConfigKey = "filesystems.disks.{$disk}.throw";
        $previousThrow = (bool) config($throwConfigKey, false);

        config([$throwConfigKey => true]);
        app('filesystem')->forgetDisk($disk);

        // Don't pass 'visibility' option - the bucket uses "Bucket owner enforced"
        // which doesn't support ACLs. Public access is controlled via bucket policy.
        $ok = Storage::disk($disk)->putFileAs($directory, $file, $filename);

        // Restore original behavior for the rest of the request.
        config([$throwConfigKey => $previousThrow]);
        app('filesystem')->forgetDisk($disk);

        if ($ok === false) {
            throw new \RuntimeException('Filesystem putFileAs returned false');
        }

        return $directory . '/' . $filename;
    }

    public function index(Request $request)
    {
        $engagementService = app(\App\Services\UserEngagementService::class);

        if (!auth()->check()) {
            // Guest: Show trending/public content only
            $query = $engagementService->getTrendingQuery();
            $query->where('is_public', true)
                ->whereNull('space_id')
                ->whereHas('category', function ($q) {
                    $q->where('slug', '!=', 'lowongan-kerja');
                });
        } else {
            $user = auth()->user();

            // Check if user has enough engagement data for personalization
            $hasEngagementData = \App\Models\UserEngagementScore::where('user_id', $user->id)->exists();

            if ($hasEngagementData && $request->sort !== 'latest') {
                // Personalized feed
                $query = $engagementService->getPersonalizedQuery($user);
            } else {
                // Fallback to latest or trending
                $query = Thread::with(['user', 'category', 'tags', 'pollOptions', 'pinnedPost.user', 'previewPosts.user', 'event.space'])
                    ->withCount(['posts', 'likes'])
                    ->where('status', 'active');

                if ($request->sort === 'trending') {
                    $query->orderByRaw('(likes_count + posts_count) DESC');
                } else {
                    $query->latest();
                }
            }

            // Filter to show general threads + threads from spaces user joined
            $query->where(function ($q) use ($user) {
                $q->whereNull('space_id')
                    ->orWhereIn('space_id', function ($sub) use ($user) {
                        $sub->select('space_id')
                            ->from('space_members')
                            ->where('user_id', $user->id);
                    });
            });
        }

        // Search logic
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Tag filter
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $threads = $query->paginate(15);
        $categories = \App\Models\Category::all();
        $announcements = \App\Models\GlobalAnnouncement::active()->latest()->get();

        return view('home', compact('threads', 'categories', 'announcements'));
    }

    /**
     * Get refreshed feed with mixed content (Fresh + Trending + Rising)
     * Ratio: 50% Fresh, 30% Trending, 20% Rising
     */
    public function refreshFeed(Request $request)
    {
        $user = auth()->user();
        $perPage = 15;

        // Calculate counts based on ratio
        $freshCount = (int) ceil($perPage * 0.5);  // 8 fresh
        $trendingCount = (int) ceil($perPage * 0.3); // 5 trending
        $risingCount = $perPage - $freshCount - $trendingCount; // 2 rising

        // Base query builder
        $baseQuery = Thread::with(['user', 'category', 'tags', 'pollOptions', 'pinnedPost.user', 'previewPosts.user', 'event.space'])
            ->withCount(['posts', 'likes'])
            ->where('status', 'active');

        // Space filter for authenticated users
        if ($user) {
            $baseQuery->where(function ($q) use ($user) {
                $q->whereNull('space_id')
                    ->orWhereIn('space_id', function ($sub) use ($user) {
                        $sub->select('space_id')
                            ->from('space_members')
                            ->where('user_id', $user->id);
                    });
            });
        } else {
            $baseQuery->where('is_public', true)->whereNull('space_id');
        }

        // Fresh threads (last 48 hours, ordered by newest)
        $fresh = (clone $baseQuery)
            ->where('created_at', '>=', now()->subHours(48))
            ->latest()
            ->take($freshCount)
            ->get();

        // Trending threads (high engagement in last 7 days)
        $freshIds = $fresh->pluck('id')->toArray();
        $trending = (clone $baseQuery)
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotIn('id', $freshIds)
            ->orderByRaw('(likes_count + posts_count) DESC')
            ->take($trendingCount)
            ->get();

        // Rising threads (new < 6 hours with at least some engagement)
        $excludeIds = array_merge($freshIds, $trending->pluck('id')->toArray());
        $rising = (clone $baseQuery)
            ->where('created_at', '>=', now()->subHours(6))
            ->whereNotIn('id', $excludeIds)
            ->whereRaw('(likes_count + posts_count) >= 1')
            ->orderByRaw('(likes_count + posts_count) DESC')
            ->take($risingCount)
            ->get();

        // Interleave the results
        $mixed = collect();
        $freshArr = $fresh->values()->all();
        $trendingArr = $trending->values()->all();
        $risingArr = $rising->values()->all();

        // Pattern: F T F R F T F F R T (interleaving)
        $pattern = ['fresh', 'trending', 'fresh', 'rising', 'fresh', 'trending', 'fresh', 'fresh', 'rising', 'trending'];
        $indices = ['fresh' => 0, 'trending' => 0, 'rising' => 0];
        $arrays = ['fresh' => $freshArr, 'trending' => $trendingArr, 'rising' => $risingArr];

        foreach ($pattern as $type) {
            if (isset($arrays[$type][$indices[$type]])) {
                $mixed->push($arrays[$type][$indices[$type]]);
                $indices[$type]++;
            }
        }

        // Add remaining items
        foreach (['fresh', 'trending', 'rising'] as $type) {
            while (isset($arrays[$type][$indices[$type]])) {
                $mixed->push($arrays[$type][$indices[$type]]);
                $indices[$type]++;
            }
        }

        // If AJAX, return rendered HTML
        if ($request->ajax()) {
            $html = '';
            foreach ($mixed as $thread) {
                $html .= view('threads.partials.thread_card', ['thread' => $thread])->render();
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $mixed->count()
            ]);
        }

        return response()->json(['threads' => $mixed]);
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('threads.create', compact('categories', 'tags'));
    }

    public function store(Request $request, \App\Services\ContentFilterService $filterService)
    {
        // Anti-spam: Rate limiting for thread creation
        $user = $request->user();
        $hourlyCount = $user->threads()->where('created_at', '>=', now()->subHour())->count();
        $dailyCount = $user->threads()->where('created_at', '>=', now()->subDay())->count();

        if ($hourlyCount >= 5) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda telah membuat terlalu banyak thread. Tunggu beberapa saat.'
                ], 429);
            }
            return back()->with('error', 'Anda telah membuat terlalu banyak thread dalam 1 jam terakhir. Tunggu beberapa saat.');
        }

        if ($dailyCount >= 20) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas harian tercapai. Anda hanya dapat membuat 20 thread per hari.'
                ], 429);
            }
            return back()->with('error', 'Batas harian tercapai. Anda hanya dapat membuat 20 thread per hari.');
        }

        $rules = [
            'category_id' => 'required_without:space_id|exists:categories,id',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,mov,avi,pdf|max:2048', // 2MB limit
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'thread_type' => 'required|in:short_thread,article',
            'video_url' => 'nullable|url',
            'poll_options' => 'nullable|array',
            'poll_options.*' => 'nullable|string|max:100',
            'space_id' => 'nullable|exists:spaces,id',
        ];

        if ($request->thread_type === 'article' || $request->space_id) {
            $rules['title'] = 'required|string|max:255';
            $rules['content'] = 'required|string|min:10';
        } else {
            $rules['title'] = 'nullable|string|max:255';
            $rules['content'] = 'required|string|max:256';
        }

        $request->validate($rules);

        $filteredTitle = $request->title ? $filterService->filter($request->title) : null;
        $filteredContent = $filterService->filter($request->input('content'));

        $imagePath = null;
        if ($request->hasFile('image')) {
            $disk = $this->threadMediaDisk();
            $path = 'threads/' . date('Y/m');

            try {
                $imagePath = $this->storeThreadMedia($request->file('image'), $disk, $path);
            } catch (\Throwable $e) {
                Log::error('Thread media upload failed', [
                    'disk' => $disk,
                    'path' => $path,
                    'userId' => optional($request->user())->id,
                    'originalName' => $request->file('image')->getClientOriginalName(),
                    'mime' => $request->file('image')->getClientMimeType(),
                    'size' => $request->file('image')->getSize(),
                    'exception' => $e,
                ]);

                return back()->with('error', 'Gagal mengupload gambar. Silakan coba lagi.')->withInput();
            }

            if (!$imagePath) {
                Log::warning('Thread media upload returned empty path', [
                    'disk' => $disk,
                    'path' => $path,
                    'userId' => optional($request->user())->id,
                    'originalName' => $request->file('image')->getClientOriginalName(),
                    'mime' => $request->file('image')->getClientMimeType(),
                    'size' => $request->file('image')->getSize(),
                ]);
                return back()->with('error', 'Gagal mengupload gambar. Silakan coba lagi.')->withInput();
            }
        }

        $thread = $request->user()->threads()->create([
            'title' => $request->thread_type === 'article' ? $filteredTitle : Str::limit($filteredContent, 50, '...'),
            'content' => $filteredContent,
            'category_id' => $request->category_id,
            'space_id' => $request->space_id,
            'type' => $request->thread_type === 'article' ? 'discussion' : 'discussion',
            'format' => $request->thread_type,
            'image' => $imagePath,
            'video_url' => $request->video_url,
            'is_public' => true,
        ]);

        if ($request->space_id) {
            \App\Models\Space::where('id', $request->space_id)->update(['last_activity_at' => now()]);
        }

        if ($request->has('tags')) {
            $thread->tags()->sync($request->tags);
        }

        if ($request->has('poll_options')) {
            $options = collect($request->poll_options)
                ->filter() // filter out null/empty
                ->map(fn($opt) => $filterService->filter($opt));

            if ($options->count() >= 2) {
                foreach ($options as $optionText) {
                    $thread->pollOptions()->create([
                        'option_text' => $optionText
                    ]);
                }
            }
        }

        // Check and award badges after thread creation
        app(\App\Services\BadgeService::class)->afterThreadCreated($request->user());

        if ($request->space_id) {
            $space = \App\Models\Space::find($request->space_id);
            return redirect()->route('spaces.show', $space->slug)->with('success', 'Diskusi berhasil dibuat!');
        }

        return redirect()->route('home')->with('success', 'Thread berhasil diposting!');
    }

    public function show(Thread $thread)
    {
        if (!auth()->check() && !$thread->is_public) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melihat thread ini.');
        }

        $thread->load(['user', 'posts.user', 'pollOptions.votes', 'event.space']);
        $thread->loadCount(['likes', 'posts']);
        return view('threads.show', compact('thread'));
    }

    public function vote(Request $request, Thread $thread)
    {
        $request->validate([
            'poll_option_id' => 'required|exists:poll_options,id'
        ]);

        if ($thread->userVoted(auth()->id())) {
            return response()->json(['success' => false, 'message' => 'Anda sudah memberikan suara.'], 403);
        }

        $option = PollOption::findOrFail($request->poll_option_id);

        PollVote::create([
            'user_id' => auth()->id(),
            'poll_option_id' => $option->id
        ]);

        $option->increment('votes_count');

        $thread->load('pollOptions.votes');
        $totalVotes = $thread->pollOptions->sum('votes_count');

        $html = view('threads.partials.poll_display', compact('thread', 'totalVotes'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas suara Anda!',
            'html' => $html
        ]);
    }

    public function update(Request $request, Thread $thread, \App\Services\ContentFilterService $filterService)
    {
        if (auth()->id() !== $thread->user_id) {
            abort(403);
        }

        if ($thread->created_at->diffInHours(now()) >= 1) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Waktu edit sudah habis (maksimal 1 jam).'], 403);
            }
            return back()->with('error', 'Waktu edit sudah habis (maksimal 1 jam).');
        }

        $rules = [
            'thread_type' => 'required|in:short_thread,article',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,mov,avi,pdf|max:2048', // 2MB limit
            'video_url' => 'nullable|url',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'poll_options' => 'nullable|array',
            'poll_options.*' => 'nullable|string|max:100',
        ];

        if ($request->thread_type === 'article') {
            $rules['title'] = 'required|string|max:255';
            $rules['content'] = 'required|string|min:10';
        } else {
            $rules['title'] = 'nullable|string|max:255';
            $rules['content'] = 'required|string|max:256';
        }

        $request->validate($rules);

        $filteredTitle = $request->title ? $filterService->filter($request->title) : null;
        $filteredContent = $filterService->filter($request->input('content'));

        $imagePath = $thread->image;
        
        // Handle image removal
        if ($request->input('remove_image') === '1') {
            // Delete old image
            if ($thread->image) {
                $disk = $this->threadMediaDisk();
                foreach (array_unique([$disk, 'public', 's3']) as $deleteDisk) {
                    try {
                        Storage::disk($deleteDisk)->delete($thread->image);
                    } catch (\Throwable $e) {
                        // Ignore delete errors; not critical.
                    }
                }
            }
            $imagePath = null;
        } elseif ($request->hasFile('image')) {
            $disk = $this->threadMediaDisk();
            $path = 'threads/' . date('Y/m');

            // Delete old image (best-effort across common disks)
            if ($thread->image) {
                foreach (array_unique([$disk, 'public', 's3']) as $deleteDisk) {
                    try {
                        Storage::disk($deleteDisk)->delete($thread->image);
                    } catch (\Throwable $e) {
                        // Ignore delete errors; not critical for update.
                    }
                }
            }

            try {
                $imagePath = $this->storeThreadMedia($request->file('image'), $disk, $path);
            } catch (\Throwable $e) {
                Log::error('Thread media update upload failed', [
                    'disk' => $disk,
                    'path' => $path,
                    'userId' => auth()->id(),
                    'threadId' => $thread->id,
                    'originalName' => $request->file('image')->getClientOriginalName(),
                    'mime' => $request->file('image')->getClientMimeType(),
                    'size' => $request->file('image')->getSize(),
                    'exception' => $e,
                ]);

                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Gagal mengupload gambar. Silakan coba lagi.'], 422);
                }
                return back()->with('error', 'Gagal mengupload gambar. Silakan coba lagi.');
            }
        }

        $thread->update([
            'title' => $request->thread_type === 'article' ? $filteredTitle : Str::limit($filteredContent, 50, '...'),
            'content' => $filteredContent,
            'format' => $request->thread_type,
            'category_id' => $request->category_id ?? $thread->category_id,
            'image' => $imagePath,
            'video_url' => $request->video_url,
        ]);

        // Sync Tags
        if ($request->has('tags')) {
            $thread->tags()->sync($request->tags);
        } else {
            $thread->tags()->detach();
        }

        // Handle Polls (Simple replace logic)
        if ($request->has('poll_options')) {
            $options = collect($request->poll_options)
                ->filter()
                ->map(fn($opt) => $filterService->filter($opt));

            if ($options->count() >= 2) {
                // To avoid complexity with existing votes, we'll replace only if changed?
                // For now, let's replace all if provided to match create behavior
                $thread->pollOptions()->delete();
                foreach ($options as $optionText) {
                    $thread->pollOptions()->create(['option_text' => $optionText]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thread berhasil diperbarui!',
                'thread' => $thread
            ]);
        }

        return back()->with('success', 'Thread berhasil diperbarui!');
    }

    public function destroy(Request $request, Thread $thread)
    {
        if (auth()->id() !== $thread->user_id && !auth()->user()->hasRole('global_admin')) {
            abort(403);
        }

        if ($thread->image) {
            $disk = $this->threadMediaDisk();
            foreach (array_unique([$disk, 'public', 's3']) as $deleteDisk) {
                try {
                    Storage::disk($deleteDisk)->delete($thread->image);
                } catch (\Throwable $e) {
                    // Ignore delete errors.
                }
            }
        }

        $thread->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thread berhasil dihapus!',
            ]);
        }

        return redirect()->route('home')->with('success', 'Thread berhasil dihapus!');
    }

    public function rules()
    {
        $rules = \App\Models\ForumRule::orderBy('order')->get();
        return view('rules', compact('rules'));
    }
}
