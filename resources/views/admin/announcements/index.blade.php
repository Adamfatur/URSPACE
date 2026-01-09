@extends('layouts.admin')

@section('title', 'Pengumuman Global')

@section('content')
    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
            <span class="material-icons">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
            <span class="material-icons">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Create Announcement Card --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <span class="material-icons text-sage-600">campaign</span>
                Buat Pengumuman Baru
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Judul (Opsional)</label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="Judul pengumuman...">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Isi Pengumuman <span
                                class="text-danger">*</span></label>
                        <textarea name="content" class="form-control rounded-3" rows="3"
                            placeholder="Tulis isi pengumuman di sini..." required maxlength="1000"></textarea>
                        <div class="text-muted small mt-1">Maksimal 1000 karakter</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Tipe</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="info">📢 Info (Biru)</option>
                            <option value="success">✅ Sukses (Hijau)</option>
                            <option value="warning">⚠️ Peringatan (Kuning)</option>
                            <option value="danger">🚨 Penting (Merah)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Durasi Tayang</label>
                        <input type="number" name="duration_value" class="form-control rounded-3" value="7" min="1" max="30"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Satuan</label>
                        <select name="duration_unit" class="form-select rounded-3" required>
                            <option value="hours">Jam</option>
                            <option value="days" selected>Hari</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <span class="material-icons align-middle me-1" style="font-size: 18px;">add</span>
                            Publikasikan Pengumuman
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Announcements List --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-0">Daftar Pengumuman</h5>
        </div>
        <div class="card-body p-0 pt-3">
            @if($announcements->isEmpty())
                <div class="text-center py-5 text-muted">
                    <span class="material-icons display-4 mb-2">campaign</span>
                    <p>Belum ada pengumuman.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted small text-uppercase">Pengumuman</th>
                                <th class="py-3 text-muted small text-uppercase">Tipe</th>
                                <th class="py-3 text-muted small text-uppercase">Status</th>
                                <th class="py-3 text-muted small text-uppercase">Kedaluwarsa</th>
                                <th class="text-end pe-4 py-3 text-muted small text-uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                                <tr class="{{ $announcement->isExpired() ? 'opacity-50' : '' }}">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">{{ $announcement->title ?? 'Tanpa Judul' }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 300px;">
                                            {{ Str::limit($announcement->content, 80) }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'info' => 'primary',
                                                'success' => 'success',
                                                'warning' => 'warning',
                                                'danger' => 'danger',
                                            ];
                                            $color = $typeColors[$announcement->type] ?? 'secondary';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle rounded-pill px-2">
                                            {{ ucfirst($announcement->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($announcement->isExpired())
                                            <span class="badge bg-secondary rounded-pill">Kedaluwarsa</span>
                                        @elseif(!$announcement->is_active)
                                            <span class="badge bg-warning rounded-pill">Nonaktif</span>
                                        @else
                                            <span class="badge bg-success rounded-pill">Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small {{ $announcement->isExpired() ? 'text-muted' : 'text-dark' }}">
                                            {{ $announcement->expires_at->format('d M Y, H:i') }}
                                        </span>
                                        <div class="text-muted small">{{ $announcement->expires_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            @if(!$announcement->isExpired())
                                                <form action="{{ route('admin.announcements.toggle', $announcement) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light rounded-pill px-3"
                                                        title="Toggle Aktif">
                                                        <span class="material-icons align-middle" style="font-size: 18px;">
                                                            {{ $announcement->is_active ? 'visibility_off' : 'visibility' }}
                                                        </span>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteAnnouncementModal{{ $announcement->id }}" title="Hapus">
                                                <span class="material-icons align-middle" style="font-size: 18px;">delete</span>
                                            </button>

                                            {{-- Delete Announcement Modal --}}
                                            <div class="modal fade" id="deleteAnnouncementModal{{ $announcement->id }}"
                                                tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                                    <div class="modal-content rounded-4 border-0 shadow">
                                                        <div class="modal-body text-center p-4">
                                                            <div class="text-danger mb-3">
                                                                <span class="material-icons"
                                                                    style="font-size: 48px;">campaign</span>
                                                            </div>
                                                            <h5 class="fw-bold">Hapus Pengumuman?</h5>
                                                            <p class="text-muted small">Hapus pengumuman
                                                                <strong>{{ $announcement->title ?? 'tanpa judul' }}</strong>?</p>
                                                            <div class="d-grid gap-2 mt-4">
                                                                <form
                                                                    action="{{ route('admin.announcements.destroy', $announcement) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger rounded-pill fw-bold w-100">Ya,
                                                                        Hapus</button>
                                                                </form>
                                                                <button type="button" class="btn btn-light rounded-pill"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection