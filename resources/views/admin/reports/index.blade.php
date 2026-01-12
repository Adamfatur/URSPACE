@extends('layouts.admin')

@section('title', 'Laporan - Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Manajemen Laporan</h4>
            <p class="text-muted mb-0">Overview semua laporan konten dari pengguna</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                    <p class="text-muted small mb-0">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-warning border-3">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h3>
                    <p class="text-muted small mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-success border-3">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold text-success mb-0">{{ $stats['resolved'] }}</h3>
                    <p class="text-muted small mb-0">Resolved</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-danger border-3">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold text-danger mb-0">{{ $stats['escalated'] }}</h3>
                    <p class="text-muted small mb-0">Escalated</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold mb-0">{{ $stats['today'] }}</h3>
                    <p class="text-muted small mb-0">Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold mb-0">{{ $stats['this_week'] }}</h3>
                    <p class="text-muted small mb-0">Minggu Ini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="escalated" {{ request('status') == 'escalated' ? 'selected' : '' }}>Escalated</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tipe Konten</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="Thread" {{ request('type') == 'Thread' ? 'selected' : '' }}>Thread</option>
                        <option value="Post" {{ request('type') == 'Post' ? 'selected' : '' }}>Post</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <span class="material-icons align-middle" style="font-size: 16px;">filter_alt</span> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <form id="bulkForm" action="{{ route('admin.reports.bulk-resolve') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th class="px-4 py-3 text-muted small text-uppercase">Pelapor</th>
                                <th class="px-4 py-3 text-muted small text-uppercase">Konten</th>
                                <th class="px-4 py-3 text-muted small text-uppercase">Alasan</th>
                                <th class="px-4 py-3 text-muted small text-uppercase">Status</th>
                                <th class="px-4 py-3 text-muted small text-uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-muted small text-uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                                    <tr>
                                                        <td class="px-4">
                                                            <input type="checkbox" name="report_ids[]" value="{{ $report->id }}"
                                                                class="form-check-input report-checkbox">
                                                        </td>
                                                        <td class="px-4">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center"
                                                                    style="width: 32px; height: 32px;">
                                                                    <span class="material-icons text-secondary"
                                                                        style="font-size: 18px;">person</span>
                                                                </div>
                                                                <span class="fw-medium">{{ $report->reporter?->name ?? 'Deleted' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-4">
                                                            <span
                                                                class="badge bg-{{ class_basename($report->reported_type) == 'Thread' ? 'primary' : 'info' }}-subtle text-{{ class_basename($report->reported_type) == 'Thread' ? 'primary' : 'info' }}">
                                                                {{ class_basename($report->reported_type) }}
                                                            </span>
                                                            <span class="small text-muted d-block">
                                                                {{ Str::limit($report->reported?->content ?? $report->reported?->title ?? 'Deleted', 30) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4">{{ Str::limit($report->reason, 40) }}</td>
                                                        <td class="px-4">
                                                            @if($report->status == 'pending')
                                                                <span class="badge bg-warning-subtle text-warning">Pending</span>
                                                            @elseif($report->status == 'resolved')
                                                                <span class="badge bg-success-subtle text-success">Resolved</span>
                                                            @elseif($report->status == 'dismissed')
                                                                <span class="badge bg-secondary-subtle text-secondary">Dismissed</span>
                                                            @else
                                                                <span class="badge bg-danger-subtle text-danger">Escalated</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 text-muted small">{{ $report->created_at->diffForHumans() }}</td>
                                                        <td class="px-4">
                                                            <div class="d-flex gap-1">
                                                                @if($report->status == 'pending')
                                                                    <form action="{{ route('admin.reports.resolve', $report) }}" method="POST"
                                                                        class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-success"
                                                                            title="Resolve (Tandai Selesai)">
                                                                            <span class="material-icons" style="font-size: 16px;">check</span>
                                                                        </button>
                                                                    </form>
                                                                    <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST"
                                                                        class="d-inline" onsubmit="return confirm('Tolak laporan ini (False Report)?')">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-secondary"
                                                                            title="Dismiss (Tolak Laporan)">
                                                                            <span class="material-icons" style="font-size: 16px;">close</span>
                                                                        </button>
                                                                    </form>
                                                                    <form action="{{ route('admin.reports.escalate', $report) }}" method="POST"
                                                                        class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-warning" title="Escalate">
                                                                            <span class="material-icons" style="font-size: 16px;">arrow_upward</span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                                @php
                                                                    $viewUrl = null;
                                                                    if ($report->reported) {
                                                                        if (class_basename($report->reported_type) === 'Thread') {
                                                                            $viewUrl = route('threads.show', $report->reported);
                                                                        } elseif (class_basename($report->reported_type) === 'Post' && $report->reported->thread) {
                                                                            $viewUrl = route('threads.show', $report->reported->thread) . '#post-' . $report->reported->id;
                                                                        }
                                                                    }
                                                                @endphp
                                                                @if($viewUrl)
                                                                    <a href="{{ $viewUrl }}" target="_blank" class="btn btn-sm btn-outline-primary"
                                                                        title="Lihat Konten">
                                                                        <span class="material-icons" style="font-size: 16px;">visibility</span>
                                                                    </a>
                                                                @endif
                                                                <form action="{{ route('admin.reports.delete-content', $report) }}" method="POST"
                                                                    class="d-inline" onsubmit="return confirm('Hapus konten yang dilaporkan?')">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete Content">
                                                                        <span class="material-icons" style="font-size: 16px;">delete</span>
                                                                    </button>
                                                                </form>
                                    @if(!$report->ai_analyzed_at && $report->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-primary" title="Analisis AI"
                                            onclick="analyzeReport({{ $report->id }})">
                                            <span class="material-icons" style="font-size: 16px;">psychology</span>
                                        </button>
                                    @endif
                                </div>
                                </td>
                                </tr>
                            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <span class="material-icons d-block mb-2" style="font-size: 48px;">inbox</span>
                    Tidak ada laporan ditemukan
                </td>
            </tr>
        @endforelse
        </tbody>
        </table>
    </div>

    @if($reports->count() > 0)
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-outline-success btn-sm" id="bulkResolveBtn" disabled>
                <span class="material-icons align-middle" style="font-size: 16px;">done_all</span>
                Resolve Terpilih
            </button>
            <div>{{ $reports->links() }}</div>
        </div>
    @endif
    </form>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('selectAll')?.addEventListener('change', function () {
            document.querySelectorAll('.report-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkBtn();
        });

        document.querySelectorAll('.report-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkBtn);
        });

        function updateBulkBtn() {
            const checked = document.querySelectorAll('.report-checkbox:checked').length;
            document.getElementById('bulkResolveBtn').disabled = checked === 0;
        }

        window.analyzeReport = async function(id) {
            try {
                const btn = event.target.closest('button');
                const originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                const res = await fetch(`/admin/ai/analyze-report/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    let message = `Prioritas: ${data.analysis.priority_score}/10\nSeverity: ${data.analysis.severity}\nRekomendasi: ${data.analysis.suggested_action}\nAnalisis: ${data.analysis.analysis}`;
                    
                    if (data.analysis.auto_dismissed) {
                        message = "✅ LAPORAN DITOLAK OTOMATIS (FALSE REPORT)\n\n" + message;
                    }
                    
                    alert(message);
                    location.reload();
                }
            } catch (e) {
                alert('Gagal menganalisis laporan');
                if(btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                }
            }
        }
    </script>
@endpush