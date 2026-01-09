<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap index.
     */
    public function index(): Response
    {
        $sitemaps = [
            ['url' => url('/sitemap/pages.xml'), 'lastmod' => now()->toDateString()],
            ['url' => url('/sitemap/threads.xml'), 'lastmod' => Thread::latest()->first()?->updated_at?->toDateString() ?? now()->toDateString()],
            ['url' => url('/sitemap/spaces.xml'), 'lastmod' => Space::where('is_private', false)->latest()->first()?->updated_at?->toDateString() ?? now()->toDateString()],
            ['url' => url('/sitemap/profiles.xml'), 'lastmod' => User::latest()->first()?->updated_at?->toDateString() ?? now()->toDateString()],
        ];

        return response()
            ->view('sitemap.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for static pages.
     */
    public function pages(): Response
    {
        $pages = [
            ['url' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['url' => route('rules'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['url' => route('spaces.index'), 'changefreq' => 'daily', 'priority' => '0.8'],
        ];

        return response()
            ->view('sitemap.pages', compact('pages'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for public threads.
     * Excludes threads in private spaces.
     */
    public function threads(): Response
    {
        $threads = Thread::query()
            ->where('status', '!=', 'hidden')
            ->where(function ($query) {
                $query->whereNull('space_id')
                    ->orWhereHas('space', function ($q) {
                        $q->where('is_private', false)
                            ->where('status', 'approved');
                    });
            })
            ->with('user')
            ->latest()
            ->get();

        return response()
            ->view('sitemap.threads', compact('threads'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for public spaces.
     * Excludes private spaces.
     */
    public function spaces(): Response
    {
        $spaces = Space::query()
            ->where('is_private', false)
            ->where('status', 'approved')
            ->latest()
            ->get();

        return response()
            ->view('sitemap.spaces', compact('spaces'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for user profiles.
     * All profiles are public (no sensitive data exposed in URLs).
     */
    public function profiles(): Response
    {
        $users = User::query()
            ->whereNull('is_banned')
            ->orWhere('is_banned', false)
            ->latest()
            ->get();

        return response()
            ->view('sitemap.profiles', compact('users'))
            ->header('Content-Type', 'application/xml');
    }
}
