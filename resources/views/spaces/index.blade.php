@extends('layouts.app')

@section('container_width', '1100px')

@section('content')
    <div class="container-fluid px-0 px-md-3">
        <!-- Create Space Trigger (Matches Home Create Post) -->
        @auth
            <div class="card shadow-sm rounded-4 mb-4 border-0">
                <div class="card-body p-3 d-flex gap-3 align-items-center">
                    <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center flex-shrink-0"
                        style="width: 40px; height: 40px; overflow:hidden;">
                        <img src="{{ auth()->user()->avatar_url }}" alt="User" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div class="flex-grow-1 text-muted fw-medium fs-6" style="cursor: pointer;" data-bs-toggle="modal"
                        data-bs-target="#createSpaceModal">
                        Ingin membangun komunitas apa hari ini?
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal"
                        data-bs-target="#createSpaceModal">Buat URSpace</button>
                </div>
            </div>

            @include('spaces.partials.create_modal')
        @endauth

        <!-- Search Bar (Matches Home) -->
        <div class="mb-4">
            <form action="{{ route('spaces.index') }}" method="GET">
                @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                <div class="input-group bg-white rounded-pill shadow-sm border overflow-hidden">
                    <span class="input-group-text border-0 bg-transparent ps-3">
                        <span class="material-icons text-muted">search</span>
                    </span>
                    <input type="text" name="search" class="form-control border-0 py-2 shadow-none"
                        placeholder="Cari URSpace..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('spaces.index', ['tab' => request('tab'), 'sort' => request('sort')]) }}"
                            class="input-group-text border-0 bg-transparent pe-3 text-decoration-none text-muted">
                            <span class="material-icons small">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Filter Pills (Matches Home Categories) -->
        <div class="category-scroll mb-3">
            <a href="{{ route('spaces.index', array_merge(request()->query(), ['tab' => 'discover'])) }}"
                class="category-item {{ $tab === 'discover' ? 'active' : '' }}">
                <span class="material-icons">explore</span>
                Jelajahi
            </a>
            @auth
                <a href="{{ route('spaces.index', array_merge(request()->query(), ['tab' => 'my-spaces'])) }}"
                    class="category-item {{ $tab === 'my-spaces' ? 'active' : '' }}">
                    <span class="material-icons">groups</span>
                    Ruang Saya
                </a>
                @if(auth()->user()->role === 'global_admin' || auth()->user()->role === 'univ_admin')
                    <a href="{{ route('admin.spaces.pending') }}"
                        class="category-item {{ request()->routeIs('admin.spaces.pending') ? 'active' : '' }}">
                        <span class="material-icons">fact_check</span>
                        Persetujuan
                        @php
                            $pendingCount = \App\Models\Space::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 10px;">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif
            @endauth
        </div>

        <!-- Tabs (Matches Home Trending/Latest) -->
        <div class="trending-tabs d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex gap-4">
                <a href="{{ route('spaces.index', ['tab' => $tab, 'sort' => 'latest']) }}"
                    class="tab-item {{ request('sort') === 'latest' || !request('sort') ? 'active' : '' }}">
                    Update Terbaru
                </a>
                <a href="{{ route('spaces.index', ['tab' => $tab, 'sort' => 'trending']) }}"
                    class="tab-item {{ request('sort') === 'trending' ? 'active' : '' }}">
                    Populer
                </a>
            </div>
        </div>

        <!-- Space Grid -->
        <div class="row g-3">
            @forelse($spaces as $space)
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{{ route('spaces.show', $space) }}" class="text-decoration-none d-block h-100">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden space-card">
                            <div class="space-card-img position-relative overflow-hidden" style="height: 140px;">
                                @if($space->cover_image)
                                    <div class="w-100 h-100"
                                        style="background-image: url('{{ asset('storage/' . $space->cover_image) }}'); background-size: cover; background-position: center;">
                                    </div>
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3"
                                        style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                                        <h5 class="fw-bold text-white mb-0 text-shadow text-truncate">{{ $space->name }}</h5>
                                        <small class="text-white-50">{{ $space->members_count }} Anggota</small>
                                    </div>
                                @else
                                    @php
                                        $gradients = [
                                            'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)',
                                            'linear-gradient(135deg, #3b82f6 0%, #2dd4bf 100%)',
                                            'linear-gradient(135deg, #ef4444 0%, #f59e0b 100%)',
                                            'linear-gradient(135deg, #10b981 0%, #3b82f6 100%)',
                                            'linear-gradient(135deg, #f472b6 0%, #9333ea 100%)',
                                            'linear-gradient(135deg, #84cc16 0%, #10b981 100%)',
                                            'linear-gradient(135deg, #f97316 0%, #db2777 100%)',
                                            'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)',
                                        ];
                                        $gradientIndex = crc32($space->name) % count($gradients);
                                        $selectedGradient = $gradients[$gradientIndex];
                                    @endphp
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center p-3 text-center position-relative"
                                        style="background: {{ $selectedGradient }};">
                                        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10"
                                            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 16px 16px;">
                                        </div>
                                        <div class="position-relative z-1">
                                            <h5 class="fw-bold text-white mb-0 text-shadow">{{ $space->name }}</h5>
                                            <small class="text-white-50">{{ $space->members_count }} Anggota</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body p-3">
                                {{-- Pending Badge (only for owner) --}}
                                @if($space->isPending() && auth()->check() && $space->owner_id === auth()->id())
                                    <div class="mb-2">
                                        <span class="badge bg-warning text-dark rounded-pill">
                                            <span class="material-icons align-middle" style="font-size: 14px;">schedule</span>
                                            Menunggu Persetujuan
                                        </span>
                                    </div>
                                @endif
                                {{-- Private Badge --}}
                                @if($space->is_private)
                                    <span class="badge bg-light text-muted rounded-pill mb-2">
                                        <span class="material-icons align-middle" style="font-size: 14px;">lock</span> Privat
                                    </span>
                                @endif
                                <p class="text-muted small mb-3 line-clamp-2" style="min-height: 2.5em;">
                                    {{ $space->description }}
                                </p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-light text-secondary rounded-pill fw-normal px-2">
                                        {{ $space->threads_count }} Diskusi
                                    </span>
                                    <span class="text-primary small fw-bold">Gabung <span class="material-icons"
                                            style="font-size: 14px; vertical-align: middle;">arrow_forward</span></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm rounded-4 p-5 d-flex align-items-center justify-content-center">
                        <span class="material-icons text-secondary opacity-25 mb-3" style="font-size: 64px;">search_off</span>
                        <h5 class="fw-bold text-muted">Belum ada URSpace ditemukan</h5>
                        <p class="text-muted small">Coba kata kunci lain atau jadilah yang pertama membuat komunitas ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Reuse existing classes but ensure spacing matches Home */
        .space-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .space-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush