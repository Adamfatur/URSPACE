@extends('layouts.app')

@section('container_width', '1000px')

@section('content')
    <div class="container-fluid px-0 px-md-3">
        <div class="mb-4">
            <a href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'events']) }}"
                class="text-decoration-none text-muted fw-bold small d-flex align-items-center gap-1 hover-text-primary transition-all">
                <span class="material-icons small">arrow_back</span>
                Kembali ke Acara
            </a>
        </div>

        <div id="eventDetailContent" class="bg-white rounded-5 shadow-sm border p-1 overflow-hidden">
            @include('spaces.partials.event_details_content', ['event' => $event, 'space' => $space])
        </div>

        @push('scripts')
            <script>
                function updateRSVP(status) {
                    const rsvpActions = document.getElementById('rsvpActions');
                    if (rsvpActions) {
                        rsvpActions.style.opacity = '0.5';
                        rsvpActions.style.pointerEvents = 'none';
                    }

                    fetch("{{ route('spaces.events.rsvp', ['space' => $space->slug, 'event' => $event->uuid]) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('eventDetailContent').innerHTML = data.html;
                                if (window.showToast) showToast(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error updating RSVP:', error);
                            if (window.showToast) showToast('Gagal memperbarui status kehadiran.', 'error');
                        });
                }
            </script>
        @endpush

        {{-- Modification: Add Edit Button for Creator --}}
        @if(auth()->id() == $event->created_by)
            <div class="fixed-bottom p-4 d-flex justify-content-end pointer-events-none">
                <button
                    class="btn btn-primary rounded-pill shadow-lg px-4 fw-bold py-2 pointer-events-auto d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#editEventModal">
                    <span class="material-icons">edit</span> Edit Acara
                </button>
            </div>

            {{-- Edit Event Modal --}}
            <div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 border-0 shadow-lg">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Edit Acara</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('spaces.events.update', ['space' => $space->slug, 'event' => $event->uuid]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Judul Acara</label>
                                    <input type="text" name="title" class="form-control rounded-3 bg-light border-0"
                                        value="{{ $event->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Deskripsi</label>
                                    <textarea name="description" class="form-control rounded-3 bg-light border-0"
                                        rows="4">{{ $event->description }}</textarea>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Mulai</label>
                                        <input type="datetime-local" name="starts_at"
                                            class="form-control rounded-3 bg-light border-0"
                                            value="{{ $event->starts_at->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Selesai (Opsional)</label>
                                        <input type="datetime-local" name="ends_at"
                                            class="form-control rounded-3 bg-light border-0"
                                            value="{{ $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ganti Cover Image (Opsional)</label>
                                    <input type="file" name="cover_image" class="form-control rounded-3 bg-light border-0">
                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tipe Lokasi</label>
                                    <select name="location_type" class="form-select rounded-3 bg-light border-0">
                                        <option value="offline" {{ $event->location_type == 'offline' ? 'selected' : '' }}>Offline
                                            (Tatap Muka)</option>
                                        <option value="online" {{ $event->location_type == 'online' ? 'selected' : '' }}>Online
                                            (Daring)</option>
                                        <option value="hybrid" {{ $event->location_type == 'hybrid' ? 'selected' : '' }}>Hybrid
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Detail Lokasi / Link</label>
                                    <input type="text" name="location_detail" class="form-control rounded-3 bg-light border-0"
                                        value="{{ $event->location_detail }}" placeholder="Nama Gedung / Link Zoom">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Visibilitas</label>
                                    <select name="visibility" class="form-select rounded-3 bg-light border-0">
                                        <option value="all_members" {{ $event->visibility == 'all_members' ? 'selected' : '' }}>
                                            Semua Anggota Space</option>
                                        <option value="open" {{ $event->visibility == 'open' ? 'selected' : '' }}>Terbuka untuk
                                            Umum</option>
                                        <option value="invited" {{ $event->visibility == 'invited' ? 'selected' : '' }}>Hanya
                                            Undangan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection