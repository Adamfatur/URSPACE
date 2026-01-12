@php 
    $compact = $compact ?? false; 
    $hideAvatar = $hideAvatar ?? false;
    $showMedia = $showMedia ?? !$compact;
@endphp
<div
    class="card shadow-sm rounded-4 {{ (isset($isPinned) && $isPinned) ? 'border-warning border-2 bg-sage-50' : 'border-0' }} p-3 p-md-4 h-100 hover-shadow transition-all">
    @if(isset($isPinned) && $isPinned)
        <div class="d-flex align-items-center gap-1 text-warning-emphasis mb-2 px-1">
            <span class="material-icons" style="font-size: 16px;">push_pin</span>
            <span class="fw-bold small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">Thread Tersemat</span>
        </div>
    @endif
    <div class="d-flex justify-content-between mb-3">
        <div class="text-decoration-none text-dark d-flex gap-3 align-items-center flex-grow-1">
            @if(!$hideAvatar)
                <a href="{{ route('profile.show', $thread->user->username) }}" class="flex-shrink-0">
                    <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                        style="width: 42px; height: 42px; overflow:hidden;">
                        <img src="{{ $thread->user->avatar_url }}" alt="User" class="w-100 h-100 object-fit-cover">
                    </div>
                </a>
            @endif
            <div>
                <div class="d-flex align-items-center flex-wrap gap-1">
                    <a href="{{ route('profile.show', $thread->user->username) }}"
                        class="text-decoration-none text-dark fw-bold hover-underline">{{ $thread->user->name }}</a>
                    <a href="{{ route('profile.show', $thread->user->username) }}"
                        class="text-decoration-none text-muted small hover-underline">{{ '@' . $thread->user->username }}</a>
                </div>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="text-muted small"
                        style="font-size: 0.75rem;">{{ $thread->created_at->diffForHumans() }}</span>
                    @if(!$compact)
                        <span class="text-muted small">•</span>
                        @if($thread->category)
                            <span
                                class="badge rounded-pill bg-success-subtle text-success border border-success-subtle fw-bold px-2"
                                style="font-size: 0.7rem;">{{ $thread->category->name }}</span>
                        @elseif($thread->space)
                            <span class="text-muted small mx-1">di</span>
                            <span class="badge rounded-pill bg-white text-dark border d-flex align-items-center gap-1 px-2"
                                style="font-size: 0.75rem;">
                                <span class="material-icons text-primary" style="font-size: 14px;">dns</span>
                                {{ $thread->space->name }}
                            </span>
                        @endif
                    @endif
                    <span class="badge rounded-pill bg-light text-muted border px-2"
                        style="font-size: 0.7rem;">{{ ucfirst($thread->type) }}</span>
                </div>
            </div>
        </div>

            <div class="dropdown">
                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="material-icons">more_horiz</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow border-0 p-2">
                    {{-- Quick Profile Link --}}
                    <li>
                        <a href="{{ route('profile.show', $thread->user->username) }}"
                            class="dropdown-item d-flex align-items-center gap-2 rounded-3">
                            <span class="material-icons small">person</span> Lihat Profil
                        </a>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center gap-2 rounded-3" @auth
                            data-bs-toggle="modal" data-bs-target="#reportModal" data-id="{{ $thread->uuid }}"
                        data-type="thread" @else onclick="Forum.guestAction()" @endauth>
                            <span class="material-icons small">flag</span> Laporkan
                        </button>
                    </li>
                    @if(isset($thread->space) && auth()->check() && $thread->space->canModerate(auth()->user()))
                        <li>
                            <form
                                action="{{ route('spaces.threads.pin', ['space' => $thread->space->slug, 'thread' => $thread->uuid]) }}"
                                method="POST">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center gap-2 rounded-3">
                                    <span class="material-icons small">push_pin</span>
                                    {{ $thread->is_pinned ? 'Unpin Thread' : 'Pin Thread' }}
                                </button>
                            </form>
                        </li>
                    @endif
                    @if(auth()->id() == $thread->user_id)
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @if($thread->created_at->diffInHours(now()) < 1)
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2 rounded-3" data-bs-toggle="modal"
                                    data-bs-target="#editThreadModal" data-id="{{ $thread->uuid }}"
                                    data-title="{{ $thread->title }}" data-content="{{ $thread->content }}"
                                    data-format="{{ $thread->format }}" data-category-id="{{ $thread->category_id }}"
                                    data-space-id="{{ $thread->space_id }}" data-video-url="{{ $thread->video_url }}"
                                    data-image="{{ $thread->image_url ?? '' }}"
                                    data-tags="{{ json_encode($thread->tags->pluck('id')) }}"
                                    data-poll-options="{{ json_encode($thread->pollOptions->pluck('option_text')) }}">
                                    <span class="material-icons small">edit</span> Edit
                                </button>
                            </li>
                        @endif
                        <li>
                            <button class="dropdown-item text-danger d-flex align-items-center gap-2 rounded-3"
                                data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="{{ $thread->uuid }}">
                                <span class="material-icons small">delete</span> Hapus
                            </button>
                        </li>
                    @endif

                    {{-- Admin/Moderator Actions --}}
                    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'global_admin', 'moderator']))
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="px-2 py-1">
                            <span class="text-muted small fw-bold">🛡️ Admin</span>
                        </li>
                        @if($thread->user->isShadowBanned())
                            <li>
                                <span class="dropdown-item d-flex align-items-center gap-2 rounded-3 text-warning"
                                    style="pointer-events: none;">
                                    <span class="material-icons small">visibility_off</span> Author Shadow Banned
                                </span>
                            </li>
                        @endif
                        <li>
                            <form action="{{ route('admin.threads.hide', $thread) }}" method="POST">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center gap-2 rounded-3">
                                    <span
                                        class="material-icons small">{{ $thread->status === 'hidden' ? 'visibility' : 'visibility_off' }}</span>
                                    {{ $thread->status === 'hidden' ? 'Tampilkan' : 'Sembunyikan' }}
                                </button>
                            </form>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2 rounded-3"
                                data-bs-toggle="modal" data-bs-target="#confirmPermanentDeleteModal{{ $thread->uuid }}">
                                <span class="material-icons small">delete_forever</span> Hapus Permanen
                            </button>
                        </li>

                        {{-- Permanent Delete Modal --}}
                        <div class="modal fade" id="confirmPermanentDeleteModal{{ $thread->uuid }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-body text-center p-4">
                                        <div class="text-danger mb-3">
                                            <span class="material-icons" style="font-size: 48px;">warning_amber</span>
                                        </div>
                                        <h5 class="fw-bold text-danger">Hapus Permanen?</h5>
                                        <p class="text-muted small">Thread ini akan dihapus selamanya dari database. Tindakan ini <strong>tidak dapat dibatalkan</strong>.</p>
                                        <div class="d-grid gap-2 mt-4">
                                            <form action="{{ route('admin.threads.destroy', $thread) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100">Ya, Hapus Selamanya</button>
                                            </form>
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(auth()->id() !== $thread->user_id && !$thread->user->isShadowBanned())
                            <li>
                                <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2 rounded-3"
                                    data-bs-toggle="modal" data-bs-target="#shadowBanModal"
                                    data-user-id="{{ $thread->user->id }}" 
                                    data-user-name="{{ $thread->user->name }}"
                                    data-user-username="{{ $thread->user->username }}">
                                    <span class="material-icons small">block</span> Shadow Ban Author
                                </button>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>

        <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none text-dark d-block">
            {{-- Only show title for articles/artikels, not for short threads --}}
            @if($thread->title && $thread->format === 'article')
                <h5 class="fw-bold mb-2 lh-sm">{{ $thread->title }}</h5>
            @endif

            <div class="thread-content markdown-content text-break {{ $compact ? 'text-truncate-multi' : '' }} mb-3"
                style="line-height: 1.6; font-size: 1rem; color: #2c2c2c;">
                {!! Str::markdown($compact ? Str::limit($thread->content, 200) : $thread->content, ['html_input' => 'escape']) !!}
            </div>

            {{-- Shared Event Card --}}
            @if($thread->event)
                <div class="card border border-light shadow-xs rounded-4 mb-3 overflow-hidden bg-white hover-shadow transition-all" style="max-width: 100%;">
                    <div class="row g-0">
                        @if($thread->event->cover_image)
                            <div class="col-4 col-md-3">
                                <img src="{{ asset('storage/' . $thread->event->cover_image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $thread->event->title }}">
                            </div>
                        @endif
                        <div class="col">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="bg-primary-subtle text-primary rounded-3 text-center px-2 py-1" style="min-width: 45px;">
                                        <div class="x-small fw-bold opacity-75">{{ $thread->event->starts_at->translatedFormat('M') }}</div>
                                        <div class="fw-black lh-1">{{ $thread->event->starts_at->format('d') }}</div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $thread->event->title }}</h6>
                                        <div class="d-flex align-items-center gap-1 text-muted x-small">
                                            <span class="material-icons x-small" style="font-size: 12px;">groups</span>
                                            {{ $thread->event->space->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 text-muted x-small">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="material-icons x-small" style="font-size: 14px;">schedule</span>
                                        {{ $thread->event->starts_at->format('H:i') }} WIB
                                    </div>
                                    @if($thread->event->location_detail)
                                        <div class="d-flex align-items-center gap-1 text-truncate">
                                            <span class="material-icons x-small" style="font-size: 14px;">place</span>
                                            {{ Str::limit($thread->event->location_detail, 20) }}
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('spaces.events.show', ['space' => $thread->event->space->slug, 'event' => $thread->event->uuid]) }}" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($showMedia)
                @if($thread->image)
                    <div class="mb-3 rounded-4 overflow-hidden shadow-sm border bg-light" style="max-height: 600px;">
                        @if(Str::endsWith($thread->image, ['.mp4', '.mov', '.avi']))
                            <video src="{{ $thread->image_url }}" controls class="w-100"
                                style="max-height: 600px;"></video>
                        @elseif(Str::endsWith($thread->image, '.pdf'))
                            <div
                                class="p-5 bg-light text-center d-flex flex-column align-items-center justify-content-center h-100">
                                <span class="material-icons text-danger display-4 mb-2">picture_as_pdf</span>
                                <span class="fw-bold text-dark fs-5">Dokumen PDF</span>
                                <span class="text-muted mb-3">Klik untuk melihat preview</span>
                                <span class="btn btn-outline-secondary rounded-pill px-4">Buka Dokumen</span>
                            </div>
                        @else
                            <img src="{{ $thread->image_url }}" class="w-100"
                                style="max-height: 600px; object-fit: contain;" alt="Thread Media">
                        @endif
                    </div>
                @endif

                @if($thread->video_url)
                    <div class="mb-3 rounded-4 overflow-hidden ratio ratio-16x9 shadow-sm border">
                        @php
                            $videoId = null;
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $thread->video_url, $matches)) {
                                $videoId = $matches[1];
                            }
                        @endphp
                        @if($videoId)
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center flex-column p-5 text-center">
                                <span class="material-icons text-muted mb-2 display-6">public</span>
                                <span class="text-primary fw-bold">Buka Tautan Eksternal</span>
                                <small class="text-muted mt-1">{{ parse_url($thread->video_url, PHP_URL_HOST) }}</small>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

        </a>

        @if($thread->pollOptions->count() > 0)
            @include('threads.partials.poll_display', ['thread' => $thread])
        @endif

        @if(!$compact)
            @if($thread->tags->count() > 0)
                <div class="mb-3 d-flex flex-wrap gap-2">
                    @foreach($thread->tags as $tag)
                        <a href="{{ route('home', ['tag' => $tag->slug]) }}" class="text-decoration-none">
                            <span
                                class="badge bg-light text-primary border rounded-pill px-3 py-2 fw-normal hover-bg-primary hover-text-white transition-all">
                                #{{ $tag->name }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="d-flex align-items-center gap-4 py-2 mt-auto">
            <button type="button"
                class="btn btn-sm ps-0 text-decoration-none d-flex align-items-center gap-2 {{ auth()->check() && $thread->likes()->where('user_id', auth()->id())->exists() ? 'text-danger' : 'text-muted' }}"
                @auth onclick="Forum.toggleLike(this, 'thread', '{{ $thread->uuid }}')" @else
                onclick="Forum.guestAction()" @endauth>
                <span class="material-icons outlined" style="font-size: 20px;">
                    {{ auth()->check() && $thread->likes()->where('user_id', auth()->id())->exists() ? 'favorite' : 'favorite_border' }}
                </span>
                <span class="fw-medium count">{{ $thread->likes_count }}</span>
            </button>

            <button class="btn btn-sm text-decoration-none d-flex align-items-center gap-2 text-muted" @auth
                data-bs-toggle="modal" data-bs-target="#commentModal" data-thread-id="{{ $thread->uuid }}"
            data-thread-username="{{ $thread->user->username }}" @else onclick="Forum.guestAction()" @endauth>
                <span class="material-icons outlined" style="font-size: 20px;">chat_bubble_outline</span>
                <span class="fw-medium count">{{ $thread->posts_count }}</span>
            </button>

            <button class="btn btn-sm text-decoration-none d-flex align-items-center gap-2 text-muted"
                onclick="Forum.openShareModal('{{ route('threads.show', $thread->uuid) }}', '{{ $thread->title ?? 'Thread dari ' . $thread->user->username }}')">
                <span class="material-icons outlined" style="font-size: 20px;">share</span>
            </button>
        </div>

        @if(!$compact && $thread->posts_count > 0)
            <div class="mt-3 pt-3">
                <div class="bg-light rounded-4 p-3">
                    <div class="thread-previews">
                        @if($thread->pinnedPost)
                            <div class="d-flex gap-2 align-items-start mb-2">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 24px; height: 24px; overflow:hidden;">
                                        <img src="{{ $thread->pinnedPost->user->avatar_url }}" class="w-100 h-100 object-fit-cover">
                                    </div>
                                </div>
                                <div class="small flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold">{{ $thread->pinnedPost->user->username }}</span>
                                        <span class="badge bg-warning text-dark border border-warning px-1 rounded-1"
                                            style="font-size: 0.6rem;">PINNED</span>
                                    </div>
                                    <div class="text-dark opacity-75">{{ Str::limit($thread->pinnedPost->content, 90) }}</div>
                                </div>
                            </div>
                        @else
                            @foreach($thread->previewPosts as $preview)
                                <div class="d-flex gap-2 align-items-start mb-2 last:mb-0">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px; overflow:hidden;">
                                            <img src="{{ $preview->user->avatar_url }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    </div>
                                    <div class="small flex-grow-1">
                                        <div class="fw-bold mb-1">{{ $preview->user->username }}</div>
                                        <div class="text-dark opacity-75">{{ Str::limit($preview->content, 90) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <a href="{{ route('threads.show', $thread) }}"
                        class="text-decoration-none text-primary fw-medium small d-flex align-items-center gap-1 mt-2">
                        <span>Lihat semua {{ $thread->posts_count }} balasan</span>
                        <span class="material-icons" style="font-size: 16px;">arrow_forward</span>
                    </a>
                </div>
            </div>
        @endif
    </div>