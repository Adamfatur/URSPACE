@extends('layouts.app')

@section('container_width', '1100px')

@section('content')
<div class="container-fluid px-0 px-md-3">
    <!-- Header (Mimics Spaces Index) -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('spaces.index') }}" class="text-decoration-none text-dark d-flex align-items-center gap-2">
            <span class="material-icons text-primary" style="font-size: 32px;">explore</span>
            <div>
                <h4 class="fw-black mb-0">URSpace</h4>
                <small class="text-muted">Jelajahi komunitas di Universitas Raharja</small>
            </div>
        </a>
    </div>

    <!-- Tabs Simulation -->
    <div class="category-pills d-flex align-items-center gap-2 mb-4 overflow-auto pb-2">
        <a href="{{ route('spaces.index') }}" class="category-item text-decoration-none">
            <span class="material-icons">explore</span>
            Jelajahi
        </a>
        <a href="{{ route('spaces.index', ['tab' => 'my-spaces']) }}" class="category-item text-decoration-none">
            <span class="material-icons">groups</span>
            Ruang Saya
        </a>
        <a href="#" class="category-item active text-decoration-none">
            <span class="material-icons">fact_check</span>
            Persetujuan
            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $pendingSpaces->total() }}</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4 shadow-sm d-flex align-items-center gap-2">
            <span class="material-icons">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-3">
        @forelse($pendingSpaces as $space)
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden space-card">
                    <!-- Cover Image -->
                    <div class="space-card-img position-relative overflow-hidden" style="height: 160px;">
                        @if($space->cover_image)
                            <div class="w-100 h-100" style="background-image: url('{{ asset('storage/' . $space->cover_image) }}'); background-size: cover; background-position: center;"></div>
                        @else
                            @php
                                $gradients = [
                                    'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)',
                                    'linear-gradient(135deg, #3b82f6 0%, #2dd4bf 100%)',
                                    'linear-gradient(135deg, #ef4444 0%, #f59e0b 100%)',
                                    'linear-gradient(135deg, #10b981 0%, #3b82f6 100%)',
                                    'linear-gradient(135deg, #f472b6 0%, #9333ea 100%)',
                                    'linear-gradient(135deg, #84cc16 0%, #10b981 100%)',
                                ];
                                $selectedGradient = $gradients[crc32($space->name) % count($gradients)];
                            @endphp
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center p-3 text-center position-relative" style="background: {{ $selectedGradient }};">
                                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 16px 16px;"></div>
                            </div>
                        @endif
                        
                        <!-- Badges Overlay -->
                        <div class="position-absolute top-0 start-0 w-100 p-3 d-flex justify-content-between">
                            <span class="badge {{ $space->is_private ? 'bg-dark' : 'bg-success' }} bg-opacity-75 backdrop-blur rounded-pill">
                                <span class="material-icons align-middle" style="font-size: 14px;">{{ $space->is_private ? 'lock' : 'public' }}</span>
                                {{ $space->is_private ? 'Privat' : 'Publik' }}
                            </span>
                            <span class="badge bg-light text-dark shadow-sm rounded-pill fw-bold">
                                {{ $space->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                             <h5 class="fw-bold mb-0 text-truncate flex-grow-1">{{ $space->name }}</h5>
                             <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                    <span class="material-icons small">more_vert</span>
                                </button>
                                <ul class="dropdown-menu border-0 shadow rounded-4">
                                    <li><a class="dropdown-item" href="{{ route('spaces.show', $space) }}">Lihat Detail</a></li>
                                </ul>
                             </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="avatar-xs rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 24px; height: 24px;">
                                <span class="material-icons" style="font-size: 14px;">person</span>
                            </div>
                            <span class="small text-muted text-truncate">Oleh <span class="fw-bold text-dark">{{ $space->owner->name }}</span></span>
                        </div>
                        
                        <p class="text-muted small mb-4 line-clamp-3 flex-grow-1" style="min-height: 4.5em;">
                            {{ $space->description }}
                        </p>

                        <div class="d-flex gap-2 mt-auto">
                            <form action="{{ route('admin.spaces.approve', $space) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2">
                                    <span class="material-icons" style="font-size: 18px;">check_circle</span>
                                    Setujui
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2 px-3 py-2" 
                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $space->id }}">
                                <span class="material-icons" style="font-size: 18px;">cancel</span>
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reject Modal --}}
            <div class="modal fade" id="rejectModal{{ $space->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <form action="{{ route('admin.spaces.reject', $space) }}" method="POST">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Tolak URSpace</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted small">Berikan alasan penolakan untuk <strong>{{ $space->name }}</strong>:</p>
                                <textarea name="rejection_reason" class="form-control rounded-4 bg-light border-0 p-3" rows="3" required placeholder="Alasan penolakan..."></textarea>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <span class="material-icons text-sage-300 display-4">fact_check</span>
                    </div>
                </div>
                <h4 class="fw-bold text-sage-900">Tidak ada pengajuan</h4>
                <p class="text-muted">Semua pengajuan URSpace telah diproses.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $pendingSpaces->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .fw-black { font-weight: 800; }
    .text-sage-900 { color: #1f2c1f; }
    .text-sage-300 { color: #8da38d; }
    
    .category-pills::-webkit-scrollbar { height: 0px; }
    .category-item {
        white-space: nowrap;
        padding: 8px 16px;
        border-radius: 50px;
        background: #f3f4f6;
        color: #4b5563;
        font-weight: 500;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .category-item:hover {
        background: #e5e7eb;
        color: #1f2937;
    }
    .category-item.active {
        background: #1f2c1f;
        color: white;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .backdrop-blur {
        backdrop-filter: blur(4px);
    }
</style>
@endpush