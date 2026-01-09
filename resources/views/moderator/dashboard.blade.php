@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Moderator Dashboard</h2>

            <div class="card shadow rounded-4 border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Laporan Masuk</h5>
                </div>
                <div class="card-body">
                    @if($pendingReports->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pendingReports as $report)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Laporan dari {{ $report->reporter->username }}</h5>
                                        <small>{{ $report->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-danger">Alasan: {{ $report->reason }}</p>
                                    <small class="text-muted">Konten ID: {{ $report->reported_id }}
                                        ({{ class_basename($report->reported_type) }})</small>
                                    <div class="mt-2 d-flex gap-2">
                                        {{-- Action Buttons --}}
                                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal"
                                            data-bs-target="#deleteContentModal{{ $report->id }}">Hapus Konten</button>
                                        
                                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" data-bs-toggle="modal"
                                            data-bs-target="#banUserModal{{ $report->id }}">Ban User</button>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal"
                                            data-bs-target="#ignoreReportModal{{ $report->id }}">Abaikan</button>

                                        {{-- Delete Content Modal --}}
                                        <div class="modal fade" id="deleteContentModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-body p-4 text-center">
                                                        <div class="mb-3">
                                                            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                                                <span class="material-icons fs-1">delete_outline</span>
                                                            </div>
                                                        </div>
                                                        <h5 class="fw-bold mb-2">Hapus Konten?</h5>
                                                        <p class="text-muted mb-4">Apakah Anda yakin ingin menghapus konten ini? Tindakan ini tidak dapat dibatalkan.</p>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('moderator.reports.handle', $report) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" name="action" value="delete_content" class="btn btn-danger rounded-pill px-4 fw-bold">Ya, Hapus Konten</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Ban User Modal --}}
                                        <div class="modal fade" id="banUserModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-body p-4 text-center">
                                                        <div class="mb-3">
                                                            <div class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                                                <span class="material-icons fs-1">person_off</span>
                                                            </div>
                                                        </div>
                                                        <h5 class="fw-bold mb-2">Ban User?</h5>
                                                        <p class="text-muted mb-4">Apakah Anda yakin ingin melakukan ban terhadap user ini?</p>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('moderator.reports.handle', $report) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" name="action" value="ban_user" class="btn btn-dark rounded-pill px-4 fw-bold">Ya, Ban User</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Ignore Report Modal --}}
                                        <div class="modal fade" id="ignoreReportModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-body p-4 text-center">
                                                        <div class="mb-3">
                                                            <div class="bg-light text-muted rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                                                <span class="material-icons fs-1">visibility_off</span>
                                                            </div>
                                                        </div>
                                                        <h5 class="fw-bold mb-2">Abaikan Laporan?</h5>
                                                        <p class="text-muted small mb-4">Laporan ini akan dianggap selesai tanpa tindakan lebih lanjut.</p>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('moderator.reports.handle', $report) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" name="action" value="ignore" class="btn btn-secondary rounded-pill px-4 fw-bold">Ya, Abaikan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">Tidak ada laporan baru.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection