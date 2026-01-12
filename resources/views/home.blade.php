@extends('layouts.app')

@section('title', request('category') ? ucfirst(request('category')) . ' - Forum UR' : 'Forum UR - Wadah Diskusi Civitas Akademika Universitas Raharja')
@section('meta_description', 'Forum diskusi resmi Universitas Raharja. Tempat berbagi pengalaman, berdiskusi, dan berkolaborasi bagi mahasiswa aktif dan alumni.')
@section('meta_keywords', 'forum raharja, universitas raharja, mahasiswa raharja, diskusi kampus, alumni raharja, kampus tangerang')
@section('canonical', route('home'))

@section('container_width')
    {{ auth()->check() ? '680px' : '980px' }}
@endsection

@section('content')
    <!-- Create Post Input Trigger -->
    @auth
        <div class="card shadow-sm rounded-4 mb-4 border-0" data-bs-toggle="modal" data-bs-target="#createThreadModal">
            <div class="card-body p-3 d-flex gap-3 align-items-center cursor-pointer">
                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                    style="width: 40px; height: 40px; overflow:hidden;">
                    <img src="{{ auth()->user()->avatar_url }}" alt="User" class="w-100 h-100 object-fit-cover">
                </div>
                <div class="flex-grow-1 text-muted fw-medium">
                    Apa yang sedang terjadi, {{ explode(' ', auth()->user()->name)[0] }}?
                </div>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Post</button>
            </div>
        </div>
    @endauth

    {{-- Global Announcements --}}
    @include('layouts.partials.global_announcement')

    <div class="row">
        <!-- Main Feed Column -->
        <div class="col-lg-{{ auth()->check() ? '12' : '8' }}">
            <!-- Search Bar -->
            <div class="mb-4">
                <form action="{{ route('home') }}" method="GET">
                    <div class="input-group bg-white rounded-pill shadow-sm border overflow-hidden">
                        <span class="input-group-text border-0 bg-transparent ps-3">
                            <span class="material-icons text-muted">search</span>
                        </span>
                        <input type="text" name="search" class="form-control border-0 py-2 shadow-none"
                            placeholder="Cari thread..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('home') }}"
                                class="input-group-text border-0 bg-transparent pe-3 text-decoration-none text-muted">
                                <span class="material-icons small">close</span>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Categories Scroll -->
            <div class="category-scroll mb-3">
                <a href="{{ route('home') }}" class="category-item {{ !request('category') ? 'active' : '' }}">
                    <span class="material-icons">apps</span>
                    Semua
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('home', ['category' => $category->slug]) }}"
                        class="category-item {{ request('category') == $category->slug ? 'active' : '' }}">
                        <span class="material-icons">{{ $category->icon ?? 'label' }}</span>
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            <!-- Trending / Latest Tabs -->
            <div class="trending-tabs d-flex justify-content-between align-items-center">
                <div>
                    @if(auth()->check())
                        <a href="{{ route('home') }}" class="tab-item {{ !request('sort') ? 'active' : '' }}">Terbaru</a>
                        <a href="{{ route('home', ['sort' => 'trending']) }}"
                            class="tab-item {{ request('sort') == 'trending' ? 'active' : '' }}">Populer</a>
                    @else
                        <div class="h5 fw-bold mb-0 py-2">Sedang Hangat Dibicarakan</div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center gap-1"
                    id="refreshFeedBtn" onclick="Forum.refreshFeed()" title="Refresh timeline">
                    <span class="material-icons" style="font-size: 18px;" id="refreshIcon">refresh</span>
                    <span class="d-none d-md-inline">Refresh</span>
                </button>
            </div>

            <!-- Feed Stream -->
            <div class="d-flex flex-column gap-3" id="feedContainer">
                @forelse($threads as $thread)
                    @include('threads.partials.thread_card', ['thread' => $thread])
                @empty
                    <div class="text-center py-5 text-muted card rounded-4 border-0 shadow-sm">
                        <span class="material-icons fs-1 mb-2">forum</span>
                        <p>Belum ada thread di kategori ini.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary rounded-pill btn-sm mx-auto">Lihat Semua</a>
                    </div>
                @endforelse
            </div>

            @if($threads->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $threads->links() }}
                </div>
            @endif
        </div>

        <!-- Guest Sidebar -->
        @guest
            <div class="col-lg-4 d-none d-lg-block">
                <div class="sticky-top" style="top: 20px; z-index: 1;">
                    <!-- Welcome Card -->
                    <div class="card rounded-4 border-0 shadow-sm mb-4 bg-white overflow-hidden">
                        <div class="card-body p-4 text-center">
                            <img src="{{ asset('logo.png') }}" onerror="this.style.display='none'" alt="Forum UR" class="mb-3"
                                style="max-height: 50px;">
                            <h5 class="fw-bold mb-2">Selamat Datang di Forum UR!</h5>
                            <p class="text-muted small mb-4">Bergabunglah dengan komunitas mahasiswa Universitas Raharja.
                                Diskusi, berbagi informasi, dan temukan teman baru.</p>

                            <div class="d-grid gap-2">
                                <a href="{{ route('register') }}" class="btn btn-primary rounded-pill fw-bold">Daftar
                                    Sekarang</a>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill fw-bold">Masuk</a>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits/Info Card -->
                    <div class="card rounded-4 border-0 shadow-sm bg-white">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Kenapa gabung?</h6>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                <li class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <span class="material-icons text-primary" style="font-size: 20px;">school</span>
                                    </div>
                                    <span class="small fw-medium">Diskusi Akademik</span>
                                </li>
                                <li class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <span class="material-icons text-primary" style="font-size: 20px;">campaign</span>
                                    </div>
                                    <span class="small fw-medium">Info Kampus Terupdate</span>
                                </li>
                                <li class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light p-2 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <span class="material-icons text-primary" style="font-size: 20px;">group_add</span>
                                    </div>
                                    <span class="small fw-medium">Bangun Relasi</span>
                                </li>
                            </ul>
                            <hr class="my-4">
                            <div class="d-flex flex-wrap gap-2 text-muted" style="font-size: 0.75rem;">
                                <a href="#" class="text-decoration-none text-muted hover-underline">Tentang</a>
                                <span>&bull;</span>
                                <a href="{{ route('rules') }}"
                                    class="text-decoration-none text-muted hover-underline">Aturan</a>
                                <span>&bull;</span>
                                <a href="#" class="text-decoration-none text-muted hover-underline">Privasi</a>
                                <span>&bull;</span>
                                <span>&copy; {{ date('Y') }} Forum UR</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endguest
    </div>

@endsection