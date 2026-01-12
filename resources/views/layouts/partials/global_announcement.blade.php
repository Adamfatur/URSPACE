@if(isset($announcements) && $announcements->count() > 0)
    @foreach($announcements as $announcement)
        @php
            $typeConfig = [
                'info' => ['bg' => 'primary', 'icon' => 'campaign'],
                'success' => ['bg' => 'success', 'icon' => 'check_circle'],
                'warning' => ['bg' => 'warning', 'icon' => 'warning'],
                'danger' => ['bg' => 'danger', 'icon' => 'error'],
            ];
            $config = $typeConfig[$announcement->type] ?? $typeConfig['info'];
        @endphp
        <div class="alert alert-{{ $config['bg'] }} alert-dismissible fade show rounded-4 mb-3 border-0 shadow-sm d-flex align-items-start gap-3 global-announcement"
            data-announcement-id="{{ $announcement->id }}" role="alert">
            <span class="material-icons fs-4 mt-1 flex-shrink-0">{{ $config['icon'] }}</span>
            <div class="flex-grow-1">
                @if($announcement->title)
                    <h6 class="alert-heading fw-bold mb-1">{{ $announcement->title }}</h6>
                @endif
                <p class="mb-0 small">{{ $announcement->content }}</p>
            </div>
            <button type="button" class="btn-close" aria-label="Close"
                onclick="Forum.dismissAnnouncement({{ $announcement->id }})"></button>
        </div>
    @endforeach
@endif