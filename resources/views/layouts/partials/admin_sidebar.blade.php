<div class="admin-sidebar d-flex flex-column flex-shrink-0 shadow-sm" id="adminSidebar">
    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('admin.dashboard') }}"
            class="admin-logo text-decoration-none d-flex align-items-center gap-2">
            <div class="rounded-circle bg-sage-100 d-flex align-items-center justify-content-center"
                style="width: 32px; height: 32px;">
                <span class="material-icons text-sage-600" style="font-size: 20px;">shield</span>
            </div>
            <span class="fs-5">Admin Panel</span>
        </a>
        <button class="btn btn-link text-muted d-md-none p-4" onclick="toggleAdminSidebar()">
            <span class="material-icons">close</span>
        </button>
    </div>

    <ul class="nav flex-column mb-auto py-2">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="material-icons">dashboard</span>
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.users.index') }}"
                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="material-icons">people</span>
                Users & Shadow Ban
            </a>
        </li>
        <li>
            <a href="{{ route('admin.hidden-content.index') }}"
                class="nav-link {{ request()->routeIs('admin.hidden-content.*') ? 'active' : '' }}">
                <span class="material-icons">visibility_off</span>
                Hidden Content
            </a>
        </li>
        <li>
            <a href="{{ route('admin.announcements.index') }}"
                class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                <span class="material-icons">campaign</span>
                Pengumuman
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reports.index') }}"
                class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="material-icons">flag</span>
                Laporan
            </a>
        </li>
        <li>
            <a href="{{ route('admin.ai.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.ai.*') ? 'active' : '' }}">
                <span class="material-icons">psychology</span>
                AI Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings.index') }}"
                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <span class="material-icons">settings</span>
                Pengaturan
            </a>
        </li>
    </ul>

    <div class="p-3 mt-auto">
        <a href="{{ route('home') }}" class="nav-link text-muted border border-light bg-light justify-content-center">
            <span class="material-icons">arrow_back</span>
            Kembali ke Forum
        </a>
    </div>
</div>