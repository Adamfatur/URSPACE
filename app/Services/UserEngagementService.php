<?php

namespace App\Services;

use App\Models\UserEngagementScore;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserEngagementService
{
    /**
     * Record engagement when user interacts with content.
     *
     * @param int $userId
     * @param Thread $thread
     * @param string $action 'view', 'like', 'comment'
     */
    public function recordEngagement(int $userId, Thread $thread, string $action): void
    {
        $scores = [
            'view' => 1,
            'like' => 3,
            'comment' => 5,
        ];

        $scoreToAdd = $scores[$action] ?? 1;

        // Update category engagement
        if ($thread->category_id) {
            UserEngagementScore::updateOrCreate(
                ['user_id' => $userId, 'category_id' => $thread->category_id, 'tag_id' => null, 'author_id' => null],
                []
            );
            DB::table('user_engagement_scores')
                ->where('user_id', $userId)
                ->where('category_id', $thread->category_id)
                ->whereNull('tag_id')
                ->whereNull('author_id')
                ->increment('score', $scoreToAdd);
        }

        // Update author engagement
        UserEngagementScore::updateOrCreate(
            ['user_id' => $userId, 'category_id' => null, 'tag_id' => null, 'author_id' => $thread->user_id],
            []
        );
        DB::table('user_engagement_scores')
            ->where('user_id', $userId)
            ->whereNull('category_id')
            ->whereNull('tag_id')
            ->where('author_id', $thread->user_id)
            ->increment('score', $scoreToAdd);

        // Update tag engagement
        foreach ($thread->tags as $tag) {
            UserEngagementScore::updateOrCreate(
                ['user_id' => $userId, 'category_id' => null, 'tag_id' => $tag->id, 'author_id' => null],
                []
            );
            DB::table('user_engagement_scores')
                ->where('user_id', $userId)
                ->whereNull('category_id')
                ->where('tag_id', $tag->id)
                ->whereNull('author_id')
                ->increment('score', $scoreToAdd * 0.5); // Tags get half weight
        }
    }

    /**
     * Get personalized thread query with scoring.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getPersonalizedQuery(User $user): \Illuminate\Database\Eloquent\Builder
    {
        // Get user's top engaged categories
        $topCategories = UserEngagementScore::where('user_id', $user->id)
            ->whereNotNull('category_id')
            ->orderByDesc('score')
            ->limit(5)
            ->pluck('category_id')
            ->toArray();

        // Get user's top engaged authors
        $topAuthors = UserEngagementScore::where('user_id', $user->id)
            ->whereNotNull('author_id')
            ->orderByDesc('score')
            ->limit(10)
            ->pluck('author_id')
            ->toArray();

        // Get users that this user follows
        $followingIds = $user->following()->pluck('users.id')->toArray();

        $query = Thread::with(['user', 'category', 'tags', 'pollOptions', 'pinnedPost.user', 'previewPosts.user'])
            ->withCount(['posts', 'likes'])
            ->where('status', 'active');

        // Apply personalization scoring using raw SQL with inline subqueries
        $categoryBonus = '';
        if (!empty($topCategories)) {
            $ids = implode(',', $topCategories);
            $categoryBonus = "CASE WHEN category_id IN ({$ids}) THEN 5 ELSE 0 END";
        } else {
            $categoryBonus = '0';
        }

        $authorBonus = '';
        if (!empty($topAuthors)) {
            $ids = implode(',', $topAuthors);
            $authorBonus = "CASE WHEN user_id IN ({$ids}) THEN 3 ELSE 0 END";
        } else {
            $authorBonus = '0';
        }

        $followingBonus = '';
        if (!empty($followingIds)) {
            $ids = implode(',', $followingIds);
            $followingBonus = "CASE WHEN user_id IN ({$ids}) THEN 4 ELSE 0 END";
        } else {
            $followingBonus = '0';
        }

        // Recency decay: older posts get reduced score.
        // Must be DB-driver aware (SQLite != MySQL).
        $driver = DB::connection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // Age in days
            $ageDays = "(TIMESTAMPDIFF(SECOND, threads.created_at, NOW()) / 86400)";
            $recencyDecay = "(1.0 / (1.0 + {$ageDays}))";
        } elseif ($driver === 'pgsql') {
            $ageDays = "(EXTRACT(EPOCH FROM (NOW() - threads.created_at)) / 86400)";
            $recencyDecay = "(1.0 / (1.0 + {$ageDays}))";
        } elseif ($driver === 'sqlsrv') {
            $ageDays = "(DATEDIFF(SECOND, threads.created_at, GETDATE()) / 86400.0)";
            $recencyDecay = "(1.0 / (1.0 + {$ageDays}))";
        } else {
            // SQLite fallback
            $recencyDecay = "(1.0 / (1.0 + (julianday('now') - julianday(threads.created_at))))";
        }

        // Inline subqueries for likes and posts count (SQLite compatible)
        $likesSubquery = "(SELECT COUNT(*) FROM likes WHERE likes.likeable_id = threads.id AND likes.likeable_type = 'App\\\\Models\\\\Thread')";
        $postsSubquery = "(SELECT COUNT(*) FROM posts WHERE posts.thread_id = threads.id AND posts.deleted_at IS NULL)";

        // Engagement score using inline subqueries
        $engagementScore = "({$likesSubquery} + ({$postsSubquery} * 1.5))";

        // Final personalized score
        $personalizedScore = "({$engagementScore} + {$categoryBonus} + {$authorBonus} + {$followingBonus}) * {$recencyDecay}";

        $query->selectRaw("threads.*, ({$personalizedScore}) as personalized_score");
        $query->orderByDesc('personalized_score');

        return $query;
    }

    /**
     * Get basic trending query for guests or users with no engagement data.
     */
    public function getTrendingQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Thread::with(['user', 'category', 'tags', 'pollOptions', 'pinnedPost.user', 'previewPosts.user'])
            ->withCount(['posts', 'likes'])
            ->where('status', 'active')
            ->orderByRaw('(likes_count + posts_count) DESC');
    }
}
