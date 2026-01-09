<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class LikeController extends Controller
{
    public function toggle(Request $request, $type, $id)
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            abort(404);
        }

        $instance = new $modelClass;
        if ($instance->getRouteKeyName() === 'uuid') {
            $model = $modelClass::where('uuid', $id)->firstOrFail();
        } else {
            $model = $modelClass::findOrFail($id);
        }

        $like = $model->likes()->where('user_id', $request->user()->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $model->likes()->create([
                'user_id' => $request->user()->id,
                'type' => 'like', // Default to like, can expand to dislike later
            ]);
            $liked = true;

            // Record engagement for personalized timeline
            if ($type === 'thread') {
                app(\App\Services\UserEngagementService::class)
                    ->recordEngagement($request->user()->id, $model, 'like');
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $model->likes()->count(),
            ]);
        }

        return back();
    }

    private function getModelClass($type)
    {
        return match ($type) {
            'thread' => \App\Models\Thread::class,
            'post' => \App\Models\Post::class,
            default => null,
        };
    }
}
