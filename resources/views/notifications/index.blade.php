@extends('layouts.app')

@section('content')
@section('title', 'Notifikasi - Forum UR')
@section('container_width', '680px')

@section('content')
    <div class="d-flex flex-column gap-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-bold mb-0 text-dark">Notifikasi</h5>
            @if($notifications->count() > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none p-0 text-muted small fw-medium">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    @php
                        $isRead = $notification->read_at !== null;
                        $bgClass = $isRead ? 'bg-white' : 'bg-primary-subtle bg-opacity-10'; // Subtle highlight for unread
                        // Use specific colors for detailed interaction types
                        $iconData = match ($notification->type) {
                            'like' => ['icon' => 'favorite', 'color' => 'text-danger'],
                            'comment' => ['icon' => 'chat_bubble', 'color' => 'text-primary'],
                            'reply' => ['icon' => 'reply', 'color' => 'text-info'], // Assuming reply type exists
                            'follow' => ['icon' => 'person_add', 'color' => 'text-success'],
                            'mention' => ['icon' => 'alternate_email', 'color' => 'text-warning'],
                            'announcement' => ['icon' => 'campaign', 'color' => 'text-danger'],
                            default => ['icon' => 'notifications', 'color' => 'text-secondary']
                        };
                    @endphp

                    <div
                        class="list-group-item p-3 border-bottom {{ $bgClass }} position-relative hover-bg-light transition-all">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0 mt-1">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light"
                                    style="width: 40px; height: 40px;">
                                    <span class="material-icons {{ $iconData['color'] }}"
                                        style="font-size: 20px;">{{ $iconData['icon'] }}</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-medium text-dark" style="font-size: 0.95rem; line-height: 1.4;">
                                        {!! $notification->message !!}
                                    </span>
                                    @if(!$isRead)
                                        <span class="badge bg-primary rounded-circle p-1 ms-2"
                                            style="width: 8px; height: 8px; min-width: 8px;"></span>
                                    @endif
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>

                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST"
                                    class="stretched-link-form">
                                    @csrf
                                    <button type="submit"
                                        class="stretched-link border-0 bg-transparent p-0 m-0 w-100 h-100 position-absolute top-0 start-0 z-1"
                                        style="z-index: 1;"></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="material-icons text-muted opacity-50" style="font-size: 32px;">notifications_off</span>
                        </div>
                        <h6 class="text-dark fw-bold mb-1">Belum ada notifikasi</h6>
                        <p class="text-muted small mb-0">Aktivitas terbaru akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    @if($notifications->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection