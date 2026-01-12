<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name', 'Forum UR') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .admin-sidebar {
            width: 280px;
            height: 100vh;
            position: sticky;
            top: 0;
            background-color: #ffffff;
            border-right: 1px solid #eef2ef;
            z-index: 1045;
            transition: transform 0.3s ease-in-out;
        }

        .nav-link {
            color: #64748b;
            font-weight: 500;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border-radius: 12px;
            margin: 4px 16px;
        }

        .nav-link:hover {
            color: #4a6f4a;
            background-color: #f0fdf4;
        }

        .nav-link.active {
            color: #4a6f4a;
            background-color: #e6f6e6;
            font-weight: 600;
        }

        .nav-link .material-icons {
            font-size: 20px;
        }

        .admin-logo {
            padding: 24px;
            color: #4a6f4a;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 767.98px) {
            .admin-sidebar {
                position: fixed;
                left: 0;
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1040;
                display: none;
                backdrop-filter: blur(2px);
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-light">
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleAdminSidebar()"></div>

    <div class="d-flex">
        <!-- Admin Sidebar Partial -->
        @include('layouts.partials.admin_sidebar')

        <!-- Admin Content -->
        <div class="flex-grow-1 p-4 p-md-5" style="min-height: 100vh; overflow-y: auto;">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-5 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-white shadow-sm rounded-circle d-md-none p-2 d-flex align-items-center justify-content-center border"
                        onclick="toggleAdminSidebar()" style="width: 40px; height: 40px;">
                        <span class="material-icons text-dark">menu</span>
                    </button>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">@yield('title', 'Admin Dashboard')</h4>
                        <p class="text-muted small mb-0 d-none d-md-block">Manage and monitor your forum application.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2 border">
                        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="32" height="32"
                            style="object-fit: cover;">
                        <span class="fw-bold small text-dark d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </div>

            @yield('content')
        </div>
    </div>

    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>

</html>