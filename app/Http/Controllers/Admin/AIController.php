<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Report;
use App\Models\Post;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AIController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * AI Dashboard Overview
     */
    public function index()
    {
        // Check if Gemini is configured
        if (!$this->gemini->isConfigured()) {
            return view('admin.ai.not-configured');
        }

        // Get stats
        $stats = [
            'flagged_content' => Thread::where('ai_flagged', true)->count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'analyzed_reports' => Report::whereNotNull('ai_analyzed_at')->count(),
            'moderated_today' => Thread::whereDate('ai_moderated_at', today())->count(),
        ];

        // Recent flagged content
        $flaggedContent = Thread::where('ai_flagged', true)
            ->with('user')
            ->latest('ai_moderated_at')
            ->take(5)
            ->get();

        // High priority reports
        $priorityReports = Report::whereNotNull('ai_priority_score')
            ->where('status', 'pending')
            ->orderByDesc('ai_priority_score')
            ->with(['reporter', 'reported'])
            ->take(5)
            ->get();

        return view('admin.ai.dashboard', compact('stats', 'flaggedContent', 'priorityReports'));
    }

    /**
     * Content Moderation Queue
     */
    public function moderationQueue(Request $request)
    {
        $query = Thread::query()->with('user');

        if ($request->filter === 'flagged') {
            $query->where('ai_flagged', true);
        } elseif ($request->filter === 'unmoderated') {
            $query->whereNull('ai_moderated_at');
        }

        $threads = $query->latest()->paginate(20);

        return view('admin.ai.moderation', compact('threads'));
    }

    /**
     * Moderate a single thread
     */
    public function moderateThread(Thread $thread)
    {
        $result = $this->gemini->moderate($thread->content);

        $thread->update([
            'ai_moderation_score' => $result['score'] ?? 0,
            'ai_moderation_flags' => $result['flags'] ?? [],
            'ai_flagged' => ($result['score'] ?? 0) >= config('gemini.moderation.auto_flag_threshold'),
            'ai_moderated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'result' => $result,
            'flagged' => $thread->ai_flagged,
        ]);
    }

    /**
     * Bulk moderate unmoderated threads
     */
    public function bulkModerate(Request $request)
    {
        $limit = min($request->input('limit', 10), 50);

        $threads = Thread::whereNull('ai_moderated_at')
            ->take($limit)
            ->get();

        $results = [
            'processed' => 0,
            'flagged' => 0,
            'errors' => 0,
        ];

        foreach ($threads as $thread) {
            try {
                $result = $this->gemini->moderate($thread->content);

                $thread->update([
                    'ai_moderation_score' => $result['score'] ?? 0,
                    'ai_moderation_flags' => $result['flags'] ?? [],
                    'ai_flagged' => ($result['score'] ?? 0) >= config('gemini.moderation.auto_flag_threshold'),
                    'ai_moderated_at' => now(),
                ]);

                $results['processed']++;
                if ($thread->ai_flagged) {
                    $results['flagged']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
            }
        }

        return response()->json($results);
    }

    /**
     * AI-Assisted Report Analysis
     */
    public function reportAnalysis(Request $request)
    {
        $query = Report::with(['reporter', 'reported'])->where('status', 'pending');

        if ($request->sort === 'priority') {
            $query->orderByDesc('ai_priority_score');
        } else {
            $query->latest();
        }

        $reports = $query->paginate(20);

        return view('admin.ai.reports', compact('reports'));
    }

    /**
     * Analyze a single report
     */
    public function analyzeReport(Report $report)
    {
        $analysis = $this->gemini->analyzeReport($report);

        $updateData = [
            'ai_priority_score' => $analysis['priority_score'] ?? 5,
            'ai_suggested_action' => $analysis['suggested_action'] ?? 'review',
            'ai_analysis' => $analysis['analysis'] ?? null,
            'ai_confidence' => $analysis['confidence'] ?? 0,
            'ai_analyzed_at' => now(),
        ];

        // Auto-dismiss false reports with high confidence
        if (($analysis['is_false_report'] ?? false) === true && ($analysis['confidence'] ?? 0) >= 85) {
            $updateData['status'] = 'dismissed';
            $analysis['auto_dismissed'] = true;
        }

        $report->update($updateData);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
        ]);
    }

    /**
     * Bulk analyze pending reports
     */
    public function bulkAnalyzeReports(Request $request)
    {
        $limit = min($request->input('limit', 10), 30);

        $reports = Report::where('status', 'pending')
            ->whereNull('ai_analyzed_at')
            ->take($limit)
            ->get();

        $results = [
            'processed' => 0,
            'errors' => 0,
        ];

        foreach ($reports as $report) {
            try {
                $analysis = $this->gemini->analyzeReport($report);

                $report->update([
                    'ai_priority_score' => $analysis['priority_score'] ?? 5,
                    'ai_suggested_action' => $analysis['suggested_action'] ?? 'review',
                    'ai_analysis' => $analysis['analysis'] ?? null,
                    'ai_confidence' => $analysis['confidence'] ?? 0,
                    'ai_analyzed_at' => now(),
                ]);

                $results['processed']++;
            } catch (\Exception $e) {
                $results['errors']++;
            }
        }

        return response()->json($results);
    }

    /**
     * Generate Weekly Summary
     */
    public function generateWeeklySummary()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Gather analytics data
        $data = [
            'period' => $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y'),
            'new_users' => User::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'new_threads' => Thread::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'new_posts' => Post::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'reports_received' => Report::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'reports_resolved' => Report::where('status', 'resolved')
                ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])->count(),
            'flagged_content' => Thread::where('ai_flagged', true)
                ->whereBetween('ai_moderated_at', [$startOfWeek, $endOfWeek])->count(),
            'active_users' => User::where('last_active_at', '>=', $startOfWeek)->count(),
            'top_categories' => Thread::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->selectRaw('category_id, COUNT(*) as count')
                ->groupBy('category_id')
                ->with('category')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->pluck('category.name')
                ->toArray(),
        ];

        $summary = $this->gemini->summarizeAnalytics($data);

        // Cache the summary for a week
        Cache::put('weekly_summary_' . date('Y-W'), $summary, now()->addWeek());

        return response()->json([
            'success' => true,
            'data' => $data,
            'summary' => $summary,
        ]);
    }

    /**
     * Get cached weekly summary
     */
    public function getWeeklySummary()
    {
        $summary = Cache::get('weekly_summary_' . date('Y-W'));

        if (!$summary) {
            return response()->json([
                'success' => false,
                'message' => 'No summary available. Generate one first.',
            ]);
        }

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ]);
    }

    /**
     * Sentiment Analysis Dashboard
     */
    public function sentimentDashboard()
    {
        // Get recent posts content for sentiment analysis
        $recentContents = Thread::latest()
            ->take(50)
            ->pluck('content')
            ->toArray();

        // Check cache first
        $cacheKey = 'sentiment_analysis_' . date('Y-m-d');
        $sentiment = Cache::get($cacheKey);

        if (!$sentiment) {
            $sentiment = $this->gemini->analyzeSentiment($recentContents);
            Cache::put($cacheKey, $sentiment, now()->addHours(6));
        }

        return view('admin.ai.sentiment', compact('sentiment'));
    }

    /**
     * Refresh sentiment analysis
     */
    public function refreshSentiment()
    {
        $recentContents = Thread::latest()
            ->take(50)
            ->pluck('content')
            ->toArray();

        $sentiment = $this->gemini->analyzeSentiment($recentContents);

        $cacheKey = 'sentiment_analysis_' . date('Y-m-d');
        Cache::put($cacheKey, $sentiment, now()->addHours(6));

        return response()->json([
            'success' => true,
            'sentiment' => $sentiment,
        ]);
    }
}
