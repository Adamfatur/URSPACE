<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingReports = Report::where('status', 'pending')
            ->with(['reporter', 'reported'])
            ->latest()
            ->paginate(10);

        return view('moderator.dashboard', compact('pendingReports'));
    }

    public function handle(Request $request, Report $report)
    {
        $action = $request->input('action');

        if ($action === 'delete_content') {
            $report->reported->delete();
            $report->update(['status' => 'resolved']);
            return back()->with('success', 'Konten berhasil dihapus.');
        }

        if ($action === 'ignore') {
            $report->update(['status' => 'resolved']);
            return back()->with('success', 'Laporan diabaikan.');
        }

        if ($action === 'ban_user') {
            $user = $report->reported->user; // Assuming reported content has a user
            $user->update(['is_banned' => true]);
            $report->update(['status' => 'resolved']);
            $report->reported->delete(); // Optionally delete content too
            return back()->with('success', 'User berhasil dibanned dan konten dihapus.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}
