<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use Illuminate\Support\Facades\Log;

class BadgeService
{
    /**
     * Check and award badges after a thread is created.
     */
    public function afterThreadCreated(User $user)
    {
        try {
            // Count user's threads
            $threadCount = $user->threads()->count();

            // Example badges based on thread count
            if ($threadCount >= 1) {
                $this->awardBadge($user, 'first_post', 'First Post', 'Created their first discussion thread.');
            }

            if ($threadCount >= 10) {
                $this->awardBadge($user, 'active_poster', 'Active Poster', 'Created 10+ discussion threads.');
            }

            if ($threadCount >= 50) {
                $this->awardBadge($user, 'community_leader', 'Community Leader', 'Created 50+ discussion threads.');
            }

        } catch (\Exception $e) {
            Log::error('Error in BadgeService::afterThreadCreated: ' . $e->getMessage());
        }
    }

    /**
     * Internal method to award a badge if not already owned.
     */
    protected function awardBadge(User $user, string $slug, string $name, string $description)
    {
        // This assumes a Badge model and a pivot table (badge_user) exist.
        // If they don't exist yet, we'll silently fail or log it to prevent crashing the main flow.

        // Ensure Badge model exists before trying to use it
        if (!class_exists(Badge::class)) {
            return;
        }

        try {
            $badge = Badge::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $description, 'icon' => 'star']
            );

            if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
                $user->badges()->attach($badge->id);
                // Optionally create a notification here
            }
        } catch (\Exception $e) {
            // Pivot table might not exist yet
            Log::warning("Could not award badge '$slug' to user {$user->id}: " . $e->getMessage());
        }
    }
}
