<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('seo.defaults.title'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    @include('layouts.partials.seo')

    {{-- PWA Support --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#5e8b5e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Forum UR">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        .btn-primary {
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 600;
            color: #fff !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <!-- Desktop Sidebar -->
    <nav class="sidebar" id="sidebar">

        <div class="sidebar-inner mt-3">
            <div class="sidebar-logo pt-4">
                <span class="material-icons">diversity_1</span>
                <h2 class="h4 fw-bold m-0 sidebar-logo-text" style="color: #4a6f4a !important;">Forum UR</h2>
            </div>

            <div class="d-flex flex-column justify-content-between h-100 pb-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"
                            title="Beranda">
                            <span class="material-icons">home</span>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchModal" title="Cari">
                            <span class="material-icons">search</span>
                            <span>Cari</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('rules') ? 'active' : '' }}"
                            href="{{ route('rules') }}" title="Aturan Forum">
                            <span class="material-icons">gavel</span>
                            <span>Aturan Forum</span>
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('spaces.*') ? 'active' : '' }}"
                                href="{{ route('spaces.index') }}" title="URSpace">
                                <span class="material-icons">groups</span>
                                <span>URSpace</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal"
                                data-bs-target="#createThreadModal" title="Buat Thread">
                                <span class="material-icons">add_box</span>
                                <span>Buat Thread</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('bookmarks.*') ? 'active' : '' }}"
                                href="{{ route('bookmarks.index') }}" title="Thread Tersimpan">
                                <span class="material-icons">bookmark_border</span>
                                <span>Tersimpan</span>
                            </a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                                href="{{ route('notifications.index') }}" title="Notifikasi" id="notificationNavLink">
                                <span class="material-icons">notifications_none</span>
                                <span>Notifikasi</span>
                                @php
                                    $unreadCount = auth()->user()->notifications()->unread()->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.65rem; margin-left: -10px; margin-top: 5px;">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() == route('profile.show', auth()->user()->username) ? 'active' : '' }}"
                                href="{{ route('profile.show', auth()->user()->username) }}" title="Profil">
                                <span class="material-icons">person_outline</span>
                                <span>Profil</span>
                            </a>
                        </li>
                    @endauth
                    @if(auth()->check() && in_array(auth()->user()->role, ['global_admin', 'univ_admin', 'admin']))
                        <li class="nav-item mt-3 pt-3 border-top">
                            <a class="nav-link text-sage-900 bg-sage-50 rounded-3 mx-2 fw-bold"
                                href="{{ route('admin.dashboard') }}" title="Admin Panel">
                                <span class="material-icons text-sage-600">admin_panel_settings</span>
                                <span>Admin Panel</span>
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="px-0 pb-4 d-flex flex-column align-items-center">
                    @auth
                        <div class="px-3 w-100">
                            <div class="dropup w-100">
                                <a href="#"
                                    class="d-flex align-items-center text-decoration-none w-100 p-2 rounded-3 hover-bg-light"
                                    style="color: inherit;" data-bs-toggle="dropdown" aria-expanded="false"
                                    id="sidebarUserDropdown">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->username }}"
                                        width="40" height="40" class="rounded-circle me-2 object-fit-cover border">
                                    <div class="d-flex flex-column text-start overflow-hidden me-auto"
                                        style="min-width: 0;">
                                        <span class="fw-bold text-truncate"
                                            style="font-size: 0.9rem; color: #1f2c1f;">{{ auth()->user()->name }}</span>
                                        <span class="text-muted text-truncate"
                                            style="font-size: 0.75rem;">{{ '@' . auth()->user()->username }}</span>
                                    </div>
                                    <span class="material-icons text-muted" style="font-size: 20px;">more_horiz</span>
                                </a>
                                <ul class="dropdown-menu shadow-lg border-0 rounded-4 w-100 p-2 mb-2"
                                    aria-labelledby="sidebarUserDropdown">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 rounded-3 py-2"
                                            href="{{ route('profile.show', auth()->user()->username) }}">
                                            <span class="material-icons" style="font-size: 20px;">person_outline</span>
                                            Profil Saya
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 rounded-3 py-2 text-danger"
                                            href="#" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal">
                                            <span class="material-icons" style="font-size: 20px;">logout</span>
                                            Keluar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="px-3 w-100 sidebar-auth-container">
                            <a href="{{ route('login') }}"
                                class="btn btn-primary w-100 rounded-pill login-btn d-flex align-items-center justify-content-center gap-2"
                                title="Masuk">
                                <span class="material-icons">login</span>
                                <span class="mini-hide">Masuk</span>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="container" style="max-width: @yield('container_width', '680px'); transition: max-width 0.3s ease;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-icons">check_circle</span>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-icons">error</span>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex flex-column gap-1">
                        @foreach($errors->all() as $error)
                            <div class="d-flex align-items-center gap-2">
                                <span class="material-icons">error_outline</span>
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Mobile Bottom Nav -->
    <div class="bottom-nav">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <span class="material-icons">home</span>
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#searchModal">
            <span class="material-icons">search</span>
        </a>
        <a @auth href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#createThreadModal" @else
        href="javascript:void(0)" onclick="Forum.guestAction()" @endauth style="color: #4a6f4a;">
            <span class="material-icons" style="font-size: 32px;">add_circle</span>
        </a>
        <a href="{{ route('spaces.index') }}" class="{{ request()->routeIs('spaces.*') ? 'active' : '' }}">
            <span class="material-icons">groups</span>
        </a>
        @auth
            <a href="{{ route('profile.show', auth()->user()->username) }}">
                <span class="material-icons">person_outline</span>
            </a>
        @else
            <a href="{{ route('login') }}">
                <span class="material-icons">login</span>
            </a>
        @endauth
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="liveToast" class="toast align-items-center border-0 rounded-4 shadow" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <span id="toastIcon" class="material-icons">info</span>
                    <span id="toastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        // Toast Helper
        function showToast(message, type = 'success') {
            const toastEl = document.getElementById('liveToast');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');

            toastMessage.textContent = message;

            // Style based on type
            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-secondary', 'bg-sage-dark', 'text-white');
            if (type === 'success') {
                toastEl.classList.add('bg-success', 'text-white');
                toastIcon.textContent = 'check_circle';
            } else if (type === 'danger' || type === 'error') {
                toastEl.classList.add('bg-danger', 'text-white');
                toastIcon.textContent = 'error';
            } else {
                toastEl.classList.add('bg-sage-dark', 'text-white');
                toastIcon.textContent = 'info';
            }

            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
        }

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (registration) {
                        console.log('[SW] Registered:', registration.scope);
                    })
                    .catch(function (error) {
                        console.log('[SW] Registration failed:', error);
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar logic removed as per request
        });

    </script>

    @include('layouts.partials.modals')
    @include('layouts.partials.search_modal')
    @include('layouts.partials.scripts')
    @stack('scripts')
</body>

</html>