<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\Post;

class ReportController extends Controller
{
    public function store(Request $request, $type, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

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

        $model->reports()->create([
            'reporter_id' => $request->user()->id,
            'reason' => $request->reason,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim. Moderator akan meninjau.',
            ]);
        }

        return back()->with('success', 'Laporan berhasil dikirim. Moderator akan meninjau.');
    }

    private function getModelClass($type)
    {
        return match ($type) {
            'thread' => Thread::class,
            'post' => Post::class,
            default => null,
        };
    }
}
