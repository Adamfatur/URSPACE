<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\User;
use App\Models\Space;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->get('query');

            if (strlen($query) < 2) {
                return response()->json([
                    'threads' => [],
                    'users' => [],
                    'spaces' => []
                ]);
            }

            // 1. Search Users (Limit 5)
            $users = User::where('username', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->select('id', 'username', 'name', 'avatar')
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name,
                        'avatar' => $user->avatar_url
                    ];
                });

            // 2. Search Spaces (Limit 5)
            $spaces = Space::where('status', 'approved')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->withCount('members') // Use withCount to dynamically get members_count
                ->limit(5)
                ->get();

            // 3. Search Threads (Limit 10) - Respecting Visibility Rules
            $threadQuery = Thread::with(['user', 'space', 'event'])
                ->where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%")
                        ->orWhere('video_url', 'like', "%{$query}%")
                        ->orWhereHas('event', function ($eq) use ($query) {
                            $eq->where('title', 'like', "%{$query}%")
                                ->orWhere('description', 'like', "%{$query}%");
                        })
                        ->orWhereHas('posts', function ($pq) use ($query) {
                            $pq->where('content', 'like', "%{$query}%");
                        });
                });

            // Copied visibility logic from ThreadController
            if (!auth()->check()) {
                $threadQuery->where('is_public', true)
                    ->whereNull('space_id')
                    ->whereHas('category', function ($q) {
                        $q->where('slug', '!=', 'lowongan-kerja');
                    });
            } else {
                $threadQuery->where(function ($q) {
                    $q->whereNull('space_id')
                        ->orWhereIn('space_id', function ($sub) {
                            $sub->select('space_id')
                                ->from('space_members')
                                ->where('user_id', auth()->id());
                        });
                });
            }

            $threads = $threadQuery->latest()->limit(10)->get()->map(function ($thread) use ($query) {
                // Contextual snippet: if query is not in title/content, check posts
                $snippet = \Illuminate\Support\Str::limit($thread->content, 100);
                
                $title = (string) $thread->title;
                $content = (string) $thread->content;
                $q = strtolower((string) $query);

                if (!empty($query) && 
                    !\Illuminate\Support\Str::contains(strtolower($title), $q) && 
                    !\Illuminate\Support\Str::contains(strtolower($content), $q)) {
                    
                    $matchingPost = $thread->posts()->where('content', 'like', "%{$query}%")->first();
                    if ($matchingPost) {
                        $snippet = '...' . \Illuminate\Support\Str::limit($matchingPost->content, 100);
                    }
                }

                return [
                    'title' => $thread->title ?: ($thread->event ? $thread->event->title : 'Thread Tanpa Judul'),
                    'content' => $snippet,
                    'uuid' => $thread->uuid,
                    'type' => $thread->type,
                    'has_video' => !empty($thread->video_url),
                    'user' => [
                        'username' => $thread->user ? $thread->user->username : 'Unknown User',
                        'avatar' => $thread->user ? $thread->user->avatar_url : asset('default-avatar.png'),
                    ],
                    'created_at' => $thread->created_at->diffForHumans(),
                    'space' => $thread->space ? ['name' => $thread->space->name] : null
                ];
            });

            return response()->json([
                'users' => $users,
                'spaces' => $spaces,
                'threads' => $threads
            ]);

        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage(), ['query' => $request->get('query'), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Search error occurred',
                'users' => [],
                'spaces' => [],
                'threads' => []
            ], 500);
        }
    }}