@extends('layouts.admin')

@section('title', 'AI Moderation Dashboard')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="material-icons align-middle me-2">psychology</span>
                AI Moderation Dashboard
            </h4>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                                <span class="material-icons text-danger">flag</span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ $stats['flagged_content'] }}</h3>
                                <small class="text-muted">Konten Ditandai</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <span class="material-icons text-warning">pending</span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ $stats['pending_reports'] }}</h3>
                                <small class="text-muted">Laporan Pending</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <span class="material-icons text-info">analytics</span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ $stats['analyzed_reports'] }}</h3>
                                <small class="text-muted">Laporan Dianalisis AI</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <span class="material-icons text-success">today</span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ $stats['moderated_today'] }}</h3>
                                <small class="text-muted">Dimoderasi Hari Ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Flagged Content --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0">
                            <span class="material-icons align-middle me-2 text-danger">warning</span>
                            Konten Ditandai AI
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @forelse($flaggedContent as $thread)
                            <div class="border-bottom px-4 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <a href="{{ route('threads.show', $thread) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ Str::limit($thread->title ?: 'Thread tanpa judul', 50) }}
                                        </a>
                                        <div class="small text-muted">
                                            oleh {{ $thread->user->name ?? 'Unknown' }} • {{ $thread->ai_moderated_at?->diffForHumans() }}
                                        </div>
                                        @if($thread->ai_moderation_flags)
                                            <div class="mt-1">
                                                @foreach($thread->ai_moderation_flags as $flag)
                                                    <span class="badge bg-danger-subtle text-danger me-1">{{ $flag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <span class="badge bg-danger">Score: {{ $thread->ai_moderation_score ?? 0 }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <span class="material-icons mb-2" style="font-size: 48px;">check_circle</span>
                                <p class="mb-0">Tidak ada konten yang ditandai AI</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Priority Reports --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0">
                            <span class="material-icons align-middle me-2 text-warning">priority_high</span>
                            Laporan Prioritas Tinggi
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @forelse($priorityReports as $report)
                            <div class="border-bottom px-4 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="fw-bold">{{ class_basename($report->reported_type) }}</span>
                                        <div class="small text-muted">
                                            Dilaporkan oleh {{ $report->reporter->name ?? 'Unknown' }} • {{ $report->created_at->diffForHumans() }}
                                        </div>
                                        <div class="small">{{ Str::limit($report->reason, 80) }}</div>
                                    </div>
                                    <span class="badge bg-warning text-dark">Priority: {{ $report->ai_priority_score }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <span class="material-icons mb-2" style="font-size: 48px;">inbox</span>
                                <p class="mb-0">Tidak ada laporan prioritas tinggi</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
