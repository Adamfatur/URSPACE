@extends('layouts.app')

@section('title', $space->name . ' - URSpace - Forum UR')
@section('meta_description', Str::limit($space->description, 160))
@section('og_image', $space->cover_image_url)
@section('og_type', 'website')
@section('canonical', route('spaces.show', $space))
@section('noindex', $space->is_private)

@section('container_width', '1100px')

@section('content')
    <div class="container-fluid px-0 px-md-3">
        <!-- URSpace Header -->
        <div class="mb-4">
            <!-- Cover Image -->
            <div class="rounded-4 overflow-hidden position-relative shadow-sm" style="height: 200px;">
                @if($space->cover_image)
                    <div class="w-100 h-100"
                        style="background-image: url('{{ $space->cover_image_url }}'); background-size: cover; background-position: center;">
                    </div>
                @else
                    @php
                        $gradients = [
                            'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)', // Indigo -> Purple
                            'linear-gradient(135deg, #3b82f6 0%, #2dd4bf 100%)', // Blue -> Teal
                            'linear-gradient(135deg, #ef4444 0%, #f59e0b 100%)', // Red -> Amber
                            'linear-gradient(135deg, #10b981 0%, #3b82f6 100%)', // Emerald -> Blue
                            'linear-gradient(135deg, #f472b6 0%, #9333ea 100%)', // Pink -> Purple
                            'linear-gradient(135deg, #84cc16 0%, #10b981 100%)', // Lime -> Emerald
                            'linear-gradient(135deg, #f97316 0%, #db2777 100%)', // Orange -> Pink
                            'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)', // Cyan -> Blue
                        ];
                        $gradientIndex = crc32($space->name) % count($gradients);
                        $selectedGradient = $gradients[$gradientIndex];
                    @endphp
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center position-relative"
                        style="background: {{ $selectedGradient }};">
                        <!-- Pattern Overlay -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10"
                            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;">
                        </div>

                        <div class="text-center px-4 position-relative z-1">
                            <h1 class="fw-black text-white mb-0 display-4 text-shadow" style="letter-spacing: -1px;">
                                {{ $space->name }}
                            </h1>
                        </div>
                    </div>
                @endif

                <!-- Overlay Gradient (Only need minimal if we have image, otherwise the gradient is enough) -->
                @if($space->cover_image)
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                        style="background: linear-gradient(to bottom, rgba(0,0,0,0) 50%, rgba(0,0,0,0.6) 100%);"></div>
                @endif
            </div>

            <!-- Space Info Bar -->
            <div class="card border-0 shadow-sm rounded-4 mt-n5 mx-3 highlight-card position-relative z-index-1">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-3 justify-content-between">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <div class="bg-white rounded-4 p-2 shadow-sm d-flex align-items-center justify-content-center text-primary flex-shrink-0"
                                style="width: 56px; height: 56px;">
                                <span class="material-icons fs-2 text-sage-500">groups</span>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="fw-black text-sage-900 mb-1 fs-3">{{ $space->name }}</h2>
                                <div class="d-flex align-items-center flex-wrap gap-2 text-muted small">
                                    <span class="d-flex align-items-center gap-1 bg-light px-2 py-1 rounded-pill">
                                        <span class="material-icons"
                                            style="font-size: 14px;">{{ $space->is_private ? 'lock' : 'public' }}</span>
                                        <span>{{ $space->is_private ? 'Private' : 'Public' }}</span>
                                    </span>
                                    <span class="d-flex align-items-center gap-1 bg-light px-2 py-1 rounded-pill">
                                        <span class="material-icons" style="font-size: 14px;">people</span>
                                        <span>{{ $space->members->count() }} Anggota</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 w-100 w-md-auto mt-3 mt-md-0 justify-content-md-end">
                            @auth
                                @if($isMember)
                                    <button
                                        class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm flex-grow-1 flex-md-grow-0 d-flex align-items-center gap-2 justify-content-center"
                                        data-bs-toggle="modal" data-bs-target="#createThreadModal" data-space-id="{{ $space->id }}">
                                        <span class="material-icons fs-5">edit</span> Buat Topik
                                    </button>
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center"
                                            style="width: 42px; height: 42px;" data-bs-toggle="dropdown">
                                            <span class="material-icons text-muted">more_horiz</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 p-2">
                                            @if($isAdmin)
                                                <li>
                                                    <h6 class="dropdown-header">Admin Tools</h6>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item rounded-3 fw-medium d-flex align-items-center gap-2"
                                                        data-bs-toggle="modal" data-bs-target="#editSpaceModal">
                                                        <span class="material-icons fs-5">settings</span> Pengaturan
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item rounded-3 fw-medium d-flex align-items-center gap-2"
                                                        href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'members']) }}">
                                                        <span class="material-icons fs-5">people</span> Anggota
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                            @endif
                                            @if($space->owner_id != auth()->id())
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item rounded-3 text-danger fw-bold d-flex align-items-center gap-2"
                                                        data-bs-toggle="modal" data-bs-target="#confirmLeaveModal">
                                                        <span class="material-icons fs-5">logout</span> Keluar URSpace
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @else
                                    {{-- User is NOT a member --}}
                                    @if($space->is_private)
                                        {{-- Private Space: Request to Join --}}
                                        @if($hasPendingRequest)
                                            <button
                                                class="btn btn-outline-warning rounded-pill px-4 fw-bold w-100 d-flex align-items-center gap-2 justify-content-center"
                                                disabled>
                                                <span class="material-icons">hourglass_top</span> Menunggu Persetujuan
                                            </button>
                                        @else
                                            <form action="{{ route('spaces.request-join', $space->slug) }}" method="POST"
                                                class="d-flex w-100">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-outline-primary rounded-pill px-4 fw-bold w-100 d-flex align-items-center gap-2 justify-content-center">
                                                    <span class="material-icons">how_to_reg</span> Minta Bergabung
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        {{-- Public Space: Join Directly --}}
                                        <form action="{{ route('spaces.join', $space->slug) }}" method="POST" class="d-flex w-100">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-primary rounded-pill px-5 fw-bold w-100 shadow-sm d-flex align-items-center gap-2 justify-content-center">
                                                <span class="material-icons">add_circle</span> Gabung
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="btn btn-primary rounded-pill px-5 fw-bold w-100 shadow-sm">Masuk</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Tabs -->
                <div class="d-flex gap-4 border-bottom mb-4 px-2 overflow-auto pb-2">
                    <a href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'discussions']) }}"
                        class="text-decoration-none pb-2 {{ $tab === 'discussions' ? 'border-bottom border-2 border-dark fw-bold text-dark' : 'text-muted' }}">
                        Diskusi
                    </a>
                    <a href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'about']) }}"
                        class="fw-bold pb-2 text-decoration-none {{ $tab === 'about' ? 'text-dark border-bottom border-dark border-3' : 'text-muted' }}">
                        Tentang
                    </a>
                    <a href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'events']) }}"
                        class="text-decoration-none pb-2 {{ $tab === 'events' ? 'border-bottom border-2 border-dark fw-bold text-dark' : 'text-muted' }}">
                        Acara
                    </a>
                    <a href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'media']) }}"
                        class="text-decoration-none pb-2 {{ $tab === 'media' ? 'border-bottom border-2 border-dark fw-bold text-dark' : 'text-muted' }}">
                        Media
                    </a>
                    <a href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'members']) }}"
                        class="text-decoration-none pb-2 {{ $tab === 'members' ? 'border-bottom border-2 border-dark fw-bold text-dark' : 'text-muted' }}">
                        Anggota
                    </a>
                </div>

                <!-- Content Area -->
                @if($tab === 'about')
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-black text-sage-900 mb-3">Tentang {{ $space->name }}</h5>
                        <p class="text-muted lh-lg mb-4">{{ $space->description }}</p>

                        <h6 class="fw-bold text-dark mb-3">Informasi Tambahan</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4">
                                    <span class="material-icons text-sage-500">calendar_today</span>
                                    <div>
                                        <div class="small text-muted">Dibuat Pada</div>
                                        <div class="fw-bold">{{ $space->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4">
                                    <span class="material-icons text-sage-500">person</span>
                                    <div>
                                        <div class="small text-muted">Pemilik</div>
                                        <div class="fw-bold">{{ $space->owner->name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4">
                                    <span class="material-icons text-sage-500">groups</span>
                                    <div>
                                        <div class="small text-muted">Total Anggota</div>
                                        <div class="fw-bold">{{ $space->members->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4">
                                    <span class="material-icons text-sage-500">forum</span>
                                    <div>
                                        <div class="small text-muted">Total Diskusi</div>
                                        <div class="fw-bold">{{ $space->threads->count() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($tab === 'events')
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Acara & Kegiatan</h5>
                        @if($isMember)
                            <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#createEventModal">
                                <span class="material-icons align-middle" style="font-size: 18px;">event</span> Buat Acara
                            </button>
                        @endif
                    </div>

                    @if($upcomingEvents->count() > 0)
                        <h6 class="fw-bold text-muted mb-3">Akan Datang</h6>
                        <div class="row g-3 mb-5">
                            @if(!$isMember)
                                {{-- Non-member View: Show only 2 events, limited info --}}
                                @foreach($upcomingEvents->take(2) as $event)
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden opacity-75">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="bg-light text-muted rounded-3 text-center p-2" style="min-width: 60px;">
                                                        <div class="fw-bold small">{{ $event->starts_at->translatedFormat('M') }}</div>
                                                        <div class="fs-4 fw-black lh-1">{{ $event->starts_at->format('d') }}</div>
                                                    </div>
                                                    <span class="badge bg-light text-muted border">Locked</span>
                                                </div>
                                                <h5 class="fw-bold mb-2 text-dark">{{ $event->title }}</h5>
                                                <p class="text-muted small mb-3">Gabung untuk melihat detail.</p>
                                                <button disabled class="btn btn-light rounded-pill w-100 fw-bold btn-sm text-muted">Detail
                                                    Terkunci</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-primary-subtle">
                                        <h6 class="fw-bold text-primary mb-2">Ingin mengikuti acara ini?</h6>
                                        <p class="text-muted small mb-3">Bergabunglah dengan {{ $space->name }} untuk melihat detail
                                            lengkap dan mendaftar acara.</p>
                                        <form action="{{ route('spaces.join', $space->slug) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm btn-sm">
                                                Gabung Sekarang
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                {{-- Member View --}}
                                @foreach($upcomingEvents as $event)
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="bg-primary-subtle text-primary rounded-3 text-center p-2"
                                                        style="min-width: 60px;">
                                                        <div class="fw-bold small">{{ $event->starts_at->translatedFormat('M') }}</div>
                                                        <div class="fs-4 fw-black lh-1">{{ $event->starts_at->format('d') }}</div>
                                                    </div>
                                                    <span
                                                        class="badge bg-light text-dark border">{{ $event->visibility === 'all_members' ? 'Semua Member' : 'Terbuka' }}</span>
                                                </div>
                                                <h5 class="fw-bold mb-2">{{ $event->title }}</h5>
                                                <p class="text-muted small mb-3 line-clamp-2">{{ Str::limit($event->description, 100) }}</p>
                                                <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                                                    <span class="material-icons" style="font-size: 16px;">schedule</span>
                                                    {{ $event->starts_at->format('H:i') }}
                                                    @if($event->ends_at) - {{ $event->ends_at->format('H:i') }} @endif
                                                </div>
                                                <a href="{{ route('spaces.events.show', ['space' => $space, 'event' => $event->uuid]) }}"
                                                    class="btn btn-outline-primary rounded-pill w-100 fw-bold btn-sm">Lihat Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded-4 mb-4">
                            <span class="material-icons text-muted opacity-25" style="font-size: 48px;">event_busy</span>
                            <p class="text-muted fw-medium mt-2">Belum ada acara yang akan datang.</p>
                        </div>
                    @endif

                    @if($pastEvents->count() > 0)
                        <h6 class="fw-bold text-muted mb-3">Selesai</h6>
                        <div class="list-group list-group-flush rounded-4 overflow-hidden border-0 shadow-sm">
                            @foreach($pastEvents as $event)
                                <div class="list-group-item border-0 p-3 d-flex align-items-center gap-3">
                                    <div class="text-secondary text-center" style="min-width: 50px;">
                                        <div class="small fw-bold">{{ $event->starts_at->translatedFormat('M') }}</div>
                                        <div class="fw-bold">{{ $event->starts_at->format('d') }}</div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $event->title }}</h6>
                                        <small class="text-muted">Selesai {{ $event->starts_at->diffForHumans() }}</small>
                                    </div>
                                    <a href="{{ route('spaces.events.show', ['space' => $space, 'event' => $event->uuid]) }}"
                                        class="btn btn-sm btn-light rounded-pill">Lihat</a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                @elseif($tab === 'media')
                    <h5 class="fw-bold mb-4">Galeri Media</h5>
                    <div class="row g-2">
                        @forelse($mediaItems as $thread)
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="{{ route('threads.show', $thread) }}"
                                    class="d-block position-relative rounded-3 overflow-hidden" style="padding-top: 100%;">
                                    <img src="{{ $thread->image_url }}"
                                        class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover hover-zoom" alt="Media">
                                </a>
                            </div>
                        @empty
                            <div class="col-12 py-5 text-center">
                                <p class="text-muted">Belum ada media yang dibagikan.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        {{ $mediaItems->links() }}
                    </div>

                @elseif($tab === 'members')
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-black text-sage-900 mb-0">Semua Anggota ({{ $members->total() }})</h6>
                            @auth
                                @if($isAdmin)
                                    <button
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold d-flex align-items-center gap-1"
                                        data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                        <span class="material-icons" style="font-size: 18px;">person_add</span> Tambah Anggota
                                    </button>
                                @endif
                            @endauth
                        </div>
                        @if(auth()->check() && $isMember)
                            {{-- Pending Join Requests (Admin Only) --}}
                            @if($isAdmin && $joinRequests->count() > 0)
                                <div class="card-body border-bottom bg-warning-subtle">
                                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                        <span class="material-icons text-warning">pending</span>
                                        Permintaan Bergabung ({{ $joinRequests->count() }})
                                    </h6>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($joinRequests as $request)
                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 shadow-sm">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $request->user->avatar_url }}" class="rounded-circle"
                                                        style="width: 32px; height: 32px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold small">{{ $request->user->name }}</div>
                                                        <div class="text-muted x-small">{{ $request->created_at->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <form
                                                        action="{{ route('spaces.join-requests.approve', ['space' => $space, 'request' => $request]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                                            <span class="material-icons" style="font-size: 16px;">check</span>
                                                        </button>
                                                    </form>
                                                    <form
                                                        action="{{ route('spaces.join-requests.reject', ['space' => $space, 'request' => $request]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                                            <span class="material-icons" style="font-size: 16px;">close</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="list-group list-group-flush">
                                @foreach($members as $member)
                                    <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm rounded-circle">
                                                <img src="{{ $member->avatar_url }}" class="w-100 h-100 object-fit-cover rounded-circle">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $member->name }}</div>
                                                <div class="small text-muted">Bergabung
                                                    {{ $member->pivot->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($member->pivot->role === 'admin')
                                                <span class="badge bg-sage-100 text-sage-600 rounded-pill">Admin</span>
                                            @elseif($member->pivot->role === 'moderator')
                                                <span class="badge bg-info-subtle text-info rounded-pill">Mod</span>
                                            @else
                                                <span class="badge bg-light text-muted rounded-pill border">Member</span>
                                            @endif

                                            {{-- Moderation Actions (Preserved) --}}
                                            @if($isModerator && $member->id !== auth()->id() && $member->pivot->role !== 'admin')
                                                <div class="dropdown ms-2">
                                                    <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                                        <span class="material-icons small">more_vert</span>
                                                    </button>
                                                    <ul class="dropdown-menu border-0 shadow">
                                                        @if($isAdmin)
                                                            @if($member->pivot->role === 'member')
                                                                <li>
                                                                    <form
                                                                        action="{{ route('spaces.members.role', ['space' => $space, 'user' => $member]) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="role" value="moderator">
                                                                        <button class="dropdown-item">Jadikan Moderator</button>
                                                                    </form>
                                                                </li>
                                                            @elseif($member->pivot->role === 'moderator')
                                                                <li>
                                                                    <form
                                                                        action="{{ route('spaces.members.role', ['space' => $space, 'user' => $member]) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="role" value="member">
                                                                        <button class="dropdown-item">Turunkan ke Member</button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        @endif

                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                                                data-bs-target="#kickModal{{ $member->id }}">
                                                                Kick
                                                            </button>
                                                        </li>

                                                        {{-- Kick Modal --}}
                                                        <div class="modal fade" id="kickModal{{ $member->id }}" tabindex="-1"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content rounded-4 border-0 shadow">
                                                                    <div class="modal-body p-4 text-center">
                                                                        <div class="mb-3">
                                                                            <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                                                                                style="width: 64px; height: 64px;">
                                                                                <span class="material-icons fs-1">person_remove</span>
                                                                            </div>
                                                                        </div>
                                                                        <h5 class="fw-bold mb-2">Kick Member?</h5>
                                                                        <p class="text-muted mb-4">Anda akan mengeluarkan
                                                                            <strong>{{ $member->name }}</strong> dari space ini. Mereka
                                                                            masih bisa meminta bergabung kembali nanti.
                                                                        </p>
                                                                        <div class="d-flex gap-2 justify-content-center">
                                                                            <button type="button"
                                                                                class="btn btn-light rounded-pill px-4 fw-bold text-muted"
                                                                                data-bs-dismiss="modal">Batal</button>
                                                                            <form
                                                                                action="{{ route('spaces.members.kick', ['space' => $space, 'user' => $member]) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="btn btn-warning rounded-pill px-4 fw-bold">Ya,
                                                                                    Kick</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($isAdmin)
                                                            <li>
                                                                <button class="dropdown-item text-danger fw-bold" data-bs-toggle="modal"
                                                                    data-bs-target="#banModal{{ $member->id }}">Ban Permanen</button>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>

                                                {{-- Ban Modal Preserved --}}
                                                @if($isAdmin)
                                                    <div class="modal fade" id="banModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content rounded-4 border-0 shadow">
                                                                <form
                                                                    action="{{ route('spaces.members.ban', ['space' => $space, 'user' => $member]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="modal-header border-0">
                                                                        <h5 class="modal-title fw-bold">Ban {{ $member->name }}</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p class="text-muted small">Member yang di-ban tidak akan bisa bergabung
                                                                            kembali ke space ini.</p>
                                                                        <textarea name="reason" class="form-control rounded-4 bg-light border-0"
                                                                            placeholder="Alasan ban (opsional)" rows="3"></textarea>
                                                                    </div>
                                                                    <div class="modal-footer border-0">
                                                                        <button type="button" class="btn btn-light rounded-pill"
                                                                            data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-danger rounded-pill fw-bold">Ban
                                                                            Member</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif


                                            @if($isAdmin && $member->id !== $space->owner_id)
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                                        <span class="material-icons" style="font-size: 18px;">more_vert</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 rounded-3">
                                                        <li>
                                                            <h6 class="dropdown-header">Ubah Peran</h6>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('spaces.members.role', ['space' => $space, 'user' => $member]) }}"
                                                                method="POST">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="role" value="member">
                                                                <button type="submit"
                                                                    class="dropdown-item rounded-3 {{ $member->pivot->role === 'member' ? 'active' : '' }}">Member</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('spaces.members.role', ['space' => $space, 'user' => $member]) }}"
                                                                method="POST">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="role" value="moderator">
                                                                <button type="submit"
                                                                    class="dropdown-item rounded-3 {{ $member->pivot->role === 'moderator' ? 'active' : '' }}">Moderator</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('spaces.members.role', ['space' => $space, 'user' => $member]) }}"
                                                                method="POST">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="role" value="admin">
                                                                <button type="submit"
                                                                    class="dropdown-item rounded-3 {{ $member->pivot->role === 'admin' ? 'active' : '' }}">Admin</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger rounded-3 fw-bold"
                                                                data-bs-toggle="modal" data-bs-target="#removeModal{{ $member->id }}">
                                                                Keluarkan Anggota
                                                            </button>
                                                        </li>

                                                        {{-- Remove Member Modal --}}
                                                        <div class="modal fade" id="removeModal{{ $member->id }}" tabindex="-1"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content rounded-4 border-0 shadow">
                                                                    <div class="modal-body p-4 text-center">
                                                                        <div class="mb-3">
                                                                            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                                                                style="width: 64px; height: 64px;">
                                                                                <span class="material-icons fs-1">group_remove</span>
                                                                            </div>
                                                                        </div>
                                                                        <h5 class="fw-bold mb-2">Hapus Anggota?</h5>
                                                                        <p class="text-muted mb-4">Apakah Anda yakin ingin mengeluarkan
                                                                            <strong>{{ $member->name }}</strong> dari anggota URSpace ini?
                                                                        </p>
                                                                        <div class="d-flex gap-2 justify-content-center">
                                                                            <button type="button"
                                                                                class="btn btn-light rounded-pill px-4 fw-bold text-muted"
                                                                                data-bs-dismiss="modal">Batal</button>
                                                                            <form
                                                                                action="{{ route('spaces.members.remove', ['space' => $space, 'user' => $member]) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="btn btn-danger rounded-pill px-4 fw-bold">Ya,
                                                                                    Keluarkan</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($members->hasPages())
                                <div class="card-footer bg-white border-0 py-3">
                                    {{ $members->appends(['tab' => 'members'])->links() }}
                                </div>
                            @endif
                        @else
                            {{-- Non-Member/Public View --}}
                            <div class="list-group list-group-flush opacity-50">
                                {{-- Show only Owners/Admins (Limited to 3) --}}
                                @foreach($members->where('pivot.role', 'admin')->take(3) as $member)
                                    <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm rounded-circle">
                                                <img src="{{ $member->avatar_url }}" class="w-100 h-100 object-fit-cover rounded-circle">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $member->name }}</div>
                                                <div class="small text-muted">Core Team</div>
                                            </div>
                                        </div>
                                        <span class="badge bg-sage-100 text-sage-600 rounded-pill">Admin</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="card-body py-5 text-center position-relative">
                                <span class="material-icons text-muted opacity-50 mb-3" style="font-size: 48px;">lock</span>
                                <h6 class="fw-bold">Ingin melihat daftar anggota?</h6>
                                <p class="text-muted small mb-4">Silakan login atau daftar untuk melihat siapa saja yang ada di
                                    URSpace ini.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    @if(auth()->check())
                                        <form action="{{ route('spaces.join', $space->slug) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                                Gabung Sekarang
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Login</a>
                                        <a href="{{ route('register') }}"
                                            class="btn btn-outline-primary rounded-pill px-4 fw-bold">Daftar</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                @else
                    <!-- Discussion Tab -->
                    @auth
                        @if(!$isMember && $space->is_private)
                            <div class="text-center py-5">
                                <span class="material-icons text-muted opacity-50 mb-3" style="font-size: 64px;">lock</span>
                                <h5 class="fw-bold">Konten Terkunci</h5>
                                <p class="text-muted">Gabung ke URSpace ini untuk melihat diskusi.</p>
                            </div>
                        @else
                            {{-- Public Space but NOT Member: Preview Mode --}}
                            @if(!$isMember)
                                <div class="d-flex flex-column gap-3">
                                    {{-- Pinned Threads (Visible to all) --}}
                                    @foreach($pinnedThreads as $thread)
                                        <div class="pinned-thread">
                                            @include('threads.partials.thread_card', ['thread' => $thread, 'isPinned' => true])
                                        </div>
                                    @endforeach

                                    {{-- Limited Thread Preview (Max 2) --}}
                                    @if($threads->count() > 0)
                                        <div class="d-flex align-items-center gap-3 my-2">
                                            <hr class="flex-grow-1 text-muted opacity-25">
                                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Diskusi Terbaru
                                                (Preview)</span>
                                            <hr class="flex-grow-1 text-muted opacity-25">
                                        </div>

                                        @foreach($threads->take(2) as $thread)
                                            <div class="position-relative">
                                                @include('threads.partials.thread_card', ['thread' => $thread])
                                                {{-- Block Interaction Overlay --}}
                                                <a href="{{ route('spaces.join', $space->slug) }}"
                                                    onclick="event.preventDefault(); document.getElementById('joinForm').submit();"
                                                    class="position-absolute top-0 start-0 w-100 h-100 z-1" style="cursor: pointer;"></a>
                                            </div>
                                        @endforeach

                                        {{-- Join CTA Overlay --}}
                                        <div
                                            class="card border-0 shadow-sm rounded-4 p-5 text-center position-relative overflow-hidden bg-white mt-2">
                                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-light opacity-50"></div>
                                            <div class="position-relative z-1">
                                                <div class="mb-3">
                                                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle rounded-circle"
                                                        style="width: 64px; height: 64px;">
                                                        <span class="material-icons text-primary fs-2">groups</span>
                                                    </div>
                                                </div>
                                                <h4 class="fw-bold text-dark">Bergabung untuk melihat lebih banyak</h4>
                                                <p class="text-muted mb-4 px-5">Anda hanya melihat sebagian kecil dari diskusi di
                                                    <strong>{{ $space->name }}</strong>. Gabung sekarang untuk akses penuh, berinteraksi, dan
                                                    melihat semua konten.
                                                </p>

                                                <form id="joinForm" action="{{ route('spaces.join', $space->slug) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                                        <span class="material-icons">add_circle</span> Gabung Space Ini
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @else
                                {{-- Member View: Full Access --}}
                                <div class="d-flex flex-column gap-3">
                                    {{-- Pinned Threads --}}
                                    @if($pinnedThreads->isNotEmpty())
                                        @foreach($pinnedThreads as $thread)
                                            <div class="pinned-thread">
                                                @include('threads.partials.thread_card', ['thread' => $thread, 'isPinned' => true])
                                            </div>
                                        @endforeach
                                        @if($threads->count() > 0)
                                            <div class="d-flex align-items-center gap-3 my-2">
                                                <hr class="flex-grow-1 text-muted opacity-25">
                                                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Diskusi
                                                    Terbaru</span>
                                                <hr class="flex-grow-1 text-muted opacity-25">
                                            </div>
                                        @endif
                                    @endif

                                    @forelse($threads as $thread)
                                        @include('threads.partials.thread_card', ['thread' => $thread])
                                    @empty
                                        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                                            <div class="mb-3">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle"
                                                    style="width: 80px; height: 80px;">
                                                    <span class="material-icons text-muted opacity-50 fs-1">forum</span>
                                                </div>
                                            </div>
                                            <h4 class="fw-bold text-dark">Belum ada diskusi</h4>
                                            <p class="text-muted mb-4">Mulai percakapan pertama untuk menghidupkan komunitas ini!</p>
                                            @if($isMember)
                                                <button class="btn btn-outline-primary rounded-pill px-4 fw-bold" data-bs-toggle="modal"
                                                    data-bs-target="#createThreadModal" data-space-id="{{ $space->id }}">
                                                    Mulai Diskusi
                                                </button>
                                            @endif
                                        </div>
                                    @endforelse

                                    <div class="mt-4">
                                        {{ $threads->links() }}
                                    </div>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                            <div class="mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center bg-sage-50 rounded-circle"
                                    style="width: 80px; height: 80px;">
                                    <span class="material-icons text-sage-600 fs-1">lock</span>
                                </div>
                            </div>
                            <h4 class="fw-bold text-dark">Eksklusif untuk Komunitas</h4>
                            <p class="text-muted mb-4">Silakan login atau buat akun untuk melihat diskusi dan materi di dalam
                                <strong>{{ $space->name }}</strong>.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Login</a>
                                <a href="{{ route('register') }}"
                                    class="btn btn-outline-primary rounded-pill px-5 fw-bold">Daftar</a>
                            </div>
                        </div>
                    @endauth
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- About Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3">Tentang</h6>
                        <p class="text-muted small mb-0 lh-lg line-clamp-3">{{ $space->description }}</p>
                        @if(strlen($space->description) > 100)
                            <a href="{{ route('spaces.show', ['space' => $space->slug, 'tab' => 'about']) }}"
                                class="small fw-bold text-primary text-decoration-none mt-2 d-inline-block">Selengkapnya</a>
                        @endif
                        <hr class="my-3 opacity-25">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="material-icons text-muted fs-6">calendar_today</span>
                            <span class="text-muted small fw-medium">Dibuat {{ $space->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-icons text-muted fs-6">person</span>
                            <span class="text-muted small fw-medium">Oleh {{ $space->owner->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Admins -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3">Pengurus</h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $space->owner->avatar_url }}" class="rounded-circle object-fit-cover"
                                    style="width: 32px; height: 32px;">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark text-sm">{{ $space->owner->name }}</span>
                                    <span class="text-muted xs-text">Pemilik</span>
                                </div>
                            </div>
                            @foreach($space->members->where('pivot.role', 'admin')->where('id', '!=', $space->owner_id)->take(3) as $admin)
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $admin->avatar_url }}" class="rounded-circle object-fit-cover"
                                        style="width: 32px; height: 32px;">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark text-sm">{{ $admin->name }}</span>
                                        <span class="text-muted xs-text">Admin</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Space Modal -->
    @if($isAdmin)
        <div class="modal fade" id="editSpaceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-black text-sage-900 w-100 text-center">Pengaturan URSpace</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('spaces.update', $space) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body pt-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-sage-900 small">Nama URSpace</label>
                                <input type="text" name="name" class="form-control bg-light border-0 shadow-none rounded-4"
                                    value="{{ $space->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-sage-900 small">Deskripsi</label>
                                <textarea name="description" rows="4"
                                    class="form-control bg-light border-0 rounded-4 p-3 shadow-none"
                                    required>{{ $space->description }}</textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold text-sage-900 small">Ubah Cover Image</label>
                                <input type="file" name="cover_image"
                                    class="form-control bg-light border-0 rounded-4 shadow-none" accept="image/*">
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm flex-grow-1">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-black text-sage-900 w-100 text-center">Tambah Anggota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('spaces.members.add', $space) }}" method="POST">
                        @csrf
                        <div class="modal-body pt-4">
                            <p class="text-muted small mb-4 text-center">Undang pengguna lain dengan memasukkan Username atau
                                Email mereka.</p>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-sage-900 small">Username atau Email</label>
                                <input type="text" name="identifier"
                                    class="form-control bg-light border-0 shadow-none rounded-4"
                                    placeholder="e.g. brama_raharja" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-sage-900 small">Peran</label>
                                <select name="role" class="form-select bg-light border-0 shadow-none rounded-4">
                                    <option value="member">Member</option>
                                    <option value="moderator">Moderator</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm flex-grow-1">
                                Tambah Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @auth
        @include('spaces.partials.create_event_modal')

        {{-- Confirm Leave Modal --}}
        <div class="modal fade" id="confirmLeaveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 64px; height: 64px;">
                                <span class="material-icons fs-1">logout</span>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2">Yakin ingin keluar?</h5>
                        <p class="text-muted small mb-4">Anda akan kehilangan akses ke diskusi internal dan konten di
                            <strong>{{ $space->name }}</strong>.
                        </p>
                        <div class="d-grid gap-2">
                            <form action="{{ route('spaces.leave', $space->slug) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100">Ya, Keluar</button>
                            </form>
                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endauth
    @include('spaces.partials.event_view_modal')
@endsection

@push('styles')
    <style>
        .fw-black {
            font-weight: 800;
        }

        .text-sage-500 {
            color: #5e8b5e;
        }

        .text-sage-900 {
            color: #1f2c1f;
        }

        .bg-sage-linear {
            background: linear-gradient(135deg, #a3c0a3 0%, #5e8b5e 100%);
        }

        .mt-n5 {
            margin-top: -3rem !important;
        }

        .z-index-1 {
            z-index: 10;
        }

        .text-sm {
            font-size: 0.9rem;
        }

        .xs-text {
            font-size: 0.75rem;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createThreadModal = document.getElementById('createThreadModal');
            if (createThreadModal) {
                createThreadModal.addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;
                    const spaceId = button.getAttribute('data-space-id');
                    const spaceInput = createThreadModal.querySelector('input[name="space_id"]');

                    if (spaceId && spaceInput) {
                        spaceInput.value = spaceId;
                    }
                });
            }
        });
    </script>
@endpush