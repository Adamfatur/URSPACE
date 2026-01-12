@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <!-- Total Users -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted fw-bold text-uppercase small mb-1">Total Users</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ number_format($stats['users']) }}</h2>
                    </div>
                    <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center"
                        style="width: 56px; height: 56px;">
                        <span class="material-icons text-primary fs-3">people</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Threads -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted fw-bold text-uppercase small mb-1">Total Threads</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ number_format($stats['threads']) }}</h2>
                    </div>
                    <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px;">
                        <span class="material-icons text-success fs-3">forum</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Posts -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted fw-bold text-uppercase small mb-1">Total Posts</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ number_format($stats['posts']) }}</h2>
                    </div>
                    <div class="rounded-circle bg-info-subtle d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px;">
                        <span class="material-icons text-info fs-3">chat_bubble</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reports -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted fw-bold text-uppercase small mb-1">Pending Reports</h6>
                        <h2 class="fw-bold mb-0 text-danger">{{ number_format($stats['reports']) }}</h2>
                    </div>
                    <div class="rounded-circle bg-danger-subtle d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px;">
                        <span class="material-icons text-danger fs-3">report_problem</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">User Growth & Engagement</h5>
                    <span class="badge bg-light text-dark border">Last 7 Days</span>
                </div>
                <div class="card-body p-4">
                    <canvas id="userChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                 <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0">New Threads</h5>
                </div>
                <div class="card-body p-4 d-flex align-items-center">
                    <canvas id="threadChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="row g-4">
        <!-- Recent Users -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0">Newest Members</h5>
                </div>
                <div class="card-body p-0 pt-3">
                    <div class="list-group list-group-flush">
                        @foreach($recentUsers as $user)
                            <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center gap-3">
                                <img src="{{ $user->avatar_url }}" class="rounded-circle object-fit-cover" width="40" height="40">
                                <div>
                                    <h6 class="mb-0 fw-bold fs-6">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                         @if($recentUsers->isEmpty())
                            <div class="text-center py-4 text-muted">No new users yet.</div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center pb-4 pt-0">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-link text-decoration-none text-sage-600 fw-bold small">View All Users</a>
                </div>
            </div>
        </div>

        <!-- Recent Threads -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0">Recent Discussions</h5>
                </div>
                 <div class="card-body p-0 pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted small text-uppercase">Thread</th>
                                    <th class="px-4 py-3 text-muted small text-uppercase">Author</th>
                                    <th class="px-4 py-3 text-muted small text-uppercase">Date</th>
                                    <th class="px-4 py-3 text-muted small text-uppercase">Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentThreads as $thread)
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 280px;">{{ $thread->title }}</div>
                                        </td>
                                        <td class="px-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $thread->user->avatar_url }}" class="rounded-circle" width="24" height="24">
                                                <span class="small">{{ $thread->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 small text-muted">{{ $thread->created_at->format('M d, H:i') }}</td>
                                        <td class="px-4"><span class="badge bg-light text-dark border">{{ $thread->category->name ?? 'General' }}</span></td>
                                    </tr>
                                @endforeach
                                 @if($recentThreads->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No threads yet.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const dates = @json($dates);
        
        // Custom Font Defaults
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        new Chart(document.getElementById('userChart'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'New Users',
                    data: @json($userRegistrations),
                    borderColor: '#4a6f4a', // Sage Green
                    backgroundColor: 'rgba(74, 111, 74, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4a6f4a',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        new Chart(document.getElementById('threadChart'), {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'New Threads',
                    data: @json($threadCreation),
                    backgroundColor: '#86b186', // Lighter Sage
                    borderRadius: 4
                }]
            },
             options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                 scales: {
                    y: {
                        beginAtZero: true,
                         grid: { borderDash: [2, 4], color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
@endsection