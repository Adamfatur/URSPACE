@extends('layouts.admin')

@section('content')
@section('title', 'User Management')

@section('content')
    {{-- Search & Filter --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-4 ps-3 text-muted">
                            <span class="material-icons">search</span>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 rounded-end-4 py-2 shadow-none" 
                               placeholder="Search by name, username, or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <select name="filter" class="form-select rounded-4 py-2" onchange="this.form.submit()">
                            <option value="">All Users</option>
                            <option value="shadow_banned" {{ request('filter') === 'shadow_banned' ? 'selected' : '' }}>Shadow Banned</option>
                            <option value="banned" {{ request('filter') === 'banned' ? 'selected' : '' }}>Banned</option>
                            <option value="admin" {{ request('filter') === 'admin' ? 'selected' : '' }}>Admins</option>
                        </select>
                         <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-4 px-3 d-flex align-items-center" title="Reset">
                            <span class="material-icons">refresh</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card shadow-sm border-0 rounded-4">
         <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-0">Registered Users</h5>
        </div>
        <div class="card-body p-0 pt-3">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small text-uppercase">User Identity</th>
                            <th class="py-3 text-muted small text-uppercase">Role</th>
                            <th class="text-center py-3 text-muted small text-uppercase">Stats</th>
                            <th class="py-3 text-muted small text-uppercase">Status</th>
                            <th class="text-end pe-4 py-3 text-muted small text-uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" class="rounded-circle object-fit-cover shadow-sm" width="48" height="48">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="text-muted small">@ {{ $user->username }}</div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $roleColor = match($user->role) {
                                            'admin', 'global_admin' => 'danger',
                                            'univ_admin' => 'info',
                                            'moderator' => 'warning',
                                            default => 'secondary'
                                        };
                                        $roleName = match($user->role) {
                                            'global_admin' => 'Global Admin',
                                            'univ_admin' => 'Univ Admin',
                                             default => ucfirst($user->role ?? 'User')
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $roleColor }}-subtle text-{{ $roleColor }} border border-{{ $roleColor }}-subtle rounded-pill px-3">
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-3">
                                        <div title="Threads">
                                            <span class="fw-bold">{{ $user->threads_count }}</span> <span class="text-muted small">T</span>
                                        </div>
                                        <div title="Posts">
                                            <span class="fw-bold">{{ $user->posts_count }}</span> <span class="text-muted small">P</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->isShadowBanned())
                                        <span class="badge bg-dark text-white rounded-pill px-2 d-flex align-items-center gap-1 w-fit">
                                            <span class="material-icons" style="font-size: 14px;">visibility_off</span> Shadow Banned
                                        </span>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Until {{ $user->shadow_banned_until->format('M d, Y') }}</small>
                                    @elseif($user->is_banned)
                                        <span class="badge bg-danger rounded-pill">Banned</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-white border shadow-sm rounded-circle p-2" data-bs-toggle="dropdown">
                                            <span class="material-icons text-dark" style="font-size: 20px;">more_horiz</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2 mt-1">
                                            <li>
                                                <a href="{{ route('profile.show', $user->username) }}" class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2">
                                                    <span class="material-icons text-sage-600" style="font-size: 18px;">person</span> View Profile
                                                </a>
                                            </li>
                                            @if(!in_array($user->role, ['admin', 'global_admin']))
                                                <li><hr class="dropdown-divider my-1"></li>
                                                @if($user->isShadowBanned())
                                                    <li>
                                                        <form action="{{ route('admin.users.remove-shadow-ban', $user) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 text-success">
                                                                <span class="material-icons" style="font-size: 18px;">visibility</span> Remove Shadow Ban
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <button type="button" class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 text-dark"
                                                            data-bs-toggle="modal" data-bs-target="#shadowBanModal"
                                                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                                                            data-user-username="{{ $user->username }}">
                                                            <span class="material-icons" style="font-size: 18px;">visibility_off</span> Shadow Ban
                                                        </button>
                                                    </li>
                                                    <li>
                                                         <button type="button" class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 text-danger">
                                                            <span class="material-icons" style="font-size: 18px;">block</span> Ban User
                                                        </button>
                                                    </li>
                                                @endif
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-light rounded-circle p-4 mb-3">
                                            <span class="material-icons text-muted display-4">person_search</span>
                                        </div>
                                        <h5 class="text-muted fw-bold">No Users Found</h5>
                                        <p class="text-muted small">Try adjusting your search or filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
