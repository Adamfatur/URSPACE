@extends('layouts.app')

@section('container_width', $thread->space_id ? '1100px' : '680px')

@section('content')
    <div class="mb-3">
        @if($thread->space)
            <a href="{{ route('spaces.show', $thread->space->slug) }}"
                class="text-decoration-none text-muted d-flex align-items-center gap-1 hover-primary transition-all">
                <span class="material-icons">arrow_back</span> Kembali ke {{ $thread->space->name }}
            </a>
        @else
            <a href="{{ route('home') }}"
                class="text-decoration-none text-muted d-flex align-items-center gap-1 hover-primary transition-all">
                <span class="material-icons">arrow_back</span> Kembali ke Beranda
            </a>
        @endif
    </div>

    <div class="row g-4">
        <!-- Thread Content Column -->
        <div class="{{ $thread->space_id ? 'col-lg-8' : 'col-12' }}">
            <!-- Main Thread -->
            <div class="card shadow-sm rounded-4 border-0 p-4 mb-4 highlight-card">
                <div class="d-flex justify-content-between mb-3">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                            style="width: 48px; height: 48px; overflow:hidden;">
                            <img src="{{ $thread->user->avatar_url }}" alt="User" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div>
                            <div class="fw-bold">
                                <a href="{{ route('profile.show', $thread->user->username) }}"
                                    class="text-decoration-none text-dark hover-underline">{{ $thread->user->name }}</a>
                                <span class="text-muted fw-normal">
                                    <a href="{{ route('profile.show', $thread->user->username) }}"
                                        class="text-decoration-none text-muted hover-underline">{{ '@' . $thread->user->username }}</a>
                                </span>
                                @if($thread->category)
                                    <span class="badge rounded-pill bg-primary text-white ms-1 small px-2 py-1"
                                        style="font-size: 0.7rem; background-color: #5e8b5e !important;">{{ $thread->category->name }}</span>
                                @elseif($thread->space)
                                    <span class="badge rounded-pill bg-info text-white ms-1 small px-2 py-1"
                                        style="font-size: 0.7rem;">{{ $thread->space->name }}</span>
                                @endif
                                <span class="badge rounded-pill bg-light text-muted border ms-1 small"
                                    style="font-size: 0.7rem;">{{ ucfirst($thread->type) }}</span>
                            </div>
                            <div class="text-muted small">{{ $thread->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <span class="material-icons">more_vert</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 p-2">
                            <li>
                                <button class="dropdown-item d-flex align-items-center gap-2" @auth data-bs-toggle="modal"
                                data-bs-target="#reportModal" data-id="{{ $thread->uuid }}" data-type="thread" @else
                                    onclick="Forum.guestAction()" @endauth>
                                    <span class="material-icons small">flag</span> Laporkan
                                </button>
                            </li>
                            @if(auth()->id() == $thread->user_id)
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                @if($thread->created_at->diffInHours(now()) < 1)
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
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
                                    <button class="dropdown-item text-danger d-flex align-items-center gap-2"
                                        data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                        data-id="{{ $thread->uuid }}">
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
                                        <span class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                            style="pointer-events: none;">
                                            <span class="material-icons small">visibility_off</span> Author Shadow Banned
                                        </span>
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('admin.threads.hide', $thread) }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item d-flex align-items-center gap-2">
                                            <span
                                                class="material-icons small">{{ $thread->status === 'hidden' ? 'visibility' : 'visibility_off' }}</span>
                                            {{ $thread->status === 'hidden' ? 'Tampilkan' : 'Sembunyikan' }}
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2"
                                        data-bs-toggle="modal" data-bs-target="#confirmPermanentDeleteModal">
                                        <span class="material-icons small">delete_forever</span> Hapus Permanen
                                    </button>
                                </li>

                                {{-- Permanent Delete Modal --}}
                                <div class="modal fade" id="confirmPermanentDeleteModal" tabindex="-1" aria-hidden="true">
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
                                        <button type="button"
                                            class="dropdown-item text-danger d-flex align-items-center gap-2 rounded-3"
                                            data-bs-toggle="modal" data-bs-target="#shadowBanModal"
                                            data-user-id="{{ $thread->user->id }}" data-user-name="{{ $thread->user->name }}"
                                            data-user-username="{{ $thread->user->username }}">
                                            <span class="material-icons small">block</span> Shadow Ban Author
                                        </button>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </div>

                @if($thread->title && $thread->format === 'article')
                    <h3 class="fw-bold mb-3">{{ $thread->title }}</h3>
                @endif

                <div class="mb-4 text-dark fs-5 markdown-content" style="line-height: 1.6;">
                    {!! Str::markdown($thread->content, ['html_input' => 'escape']) !!}
                </div>

                {{-- Shared Event Card --}}
                @if($thread->event)
                    <div class="card border border-light shadow-sm rounded-4 mb-4 overflow-hidden bg-white hover-shadow transition-all">
                        <div class="row g-0">
                            @if($thread->event->cover_image)
                                <div class="col-md-4">
                                    <img src="{{ asset('storage/' . $thread->event->cover_image) }}" class="w-100 h-100 object-fit-cover"
                                        alt="{{ $thread->event->title }}" style="min-height: 200px;">
                                </div>
                            @endif
                            <div class="col">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="bg-primary-subtle text-primary rounded-3 text-center px-3 py-2" style="min-width: 60px;">
                                            <div class="small fw-bold opacity-75">{{ $thread->event->starts_at->translatedFormat('M') }}</div>
                                            <div class="fs-4 fw-black lh-1">{{ $thread->event->starts_at->format('d') }}</div>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1 text-dark">{{ $thread->event->title }}</h5>
                                            <div class="d-flex align-items-center gap-1 text-muted small">
                                                <span class="material-icons small">groups</span>
                                                {{ $thread->event->space->name }}
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-3 line-clamp-2">{{ Str::limit($thread->event->description, 150) }}</p>
                                    <div class="d-flex align-items-center gap-4 text-muted small mb-3">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="material-icons small">schedule</span>
                                            {{ $thread->event->starts_at->format('H:i') }} WIB
                                        </div>
                                        @if($thread->event->location_detail)
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="material-icons small">place</span>
                                                {{ $thread->event->location_detail }}
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('spaces.events.show', ['space' => $thread->event->space->slug, 'event' => $thread->event->uuid]) }}"
                                        class="btn btn-outline-primary rounded-pill fw-bold w-100">
                                        Lihat Detail Acara
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($thread->video_url)
                    <div class="mb-4 rounded-4 overflow-hidden ratio ratio-16x9 shadow-sm border">
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
                                <span class="text-primary fw-bold">Tautan Media Eksternal</span>
                                <p class="text-muted small mb-3">{{ $thread->video_url }}</p>
                                <a href="{{ $thread->video_url }}" target="_blank"
                                    class="btn btn-sm btn-primary mt-2 rounded-pill px-4 fw-bold">Buka Tautan</a>
                            </div>
                        @endif
                    </div>
                @endif

                @if($thread->pollOptions->count() > 0)
                    <div id="poll-wrapper">
                        @include('threads.partials.poll_display', ['thread' => $thread])
                    </div>
                @endif

                @if($thread->image)
                    <div class="mb-4 rounded-4 overflow-hidden">
                        @if(Str::endsWith($thread->image, ['.mp4', '.mov', '.avi']))
                            <video src="{{ $thread->image_url }}" controls class="w-100"></video>
                        @elseif(Str::endsWith($thread->image, '.pdf'))
                            <div class="ratio ratio-16x9 border rounded-4" style="min-height: 600px;">
                                <iframe src="{{ $thread->image_url }}" class="w-100 h-100 rounded-4"></iframe>
                            </div>
                            <div class="mt-2 text-end">
                                <a href="{{ $thread->image_url }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                    <span class="material-icons align-middle small">open_in_new</span> Buka di Tab Baru
                                </a>
                            </div>
                        @else
                            <img src="{{ $thread->image_url }}"
                                class="w-100 cursor-pointer hover-opacity transition-all" alt="Thread Media"
                                onclick="Forum.openImageModal('{{ $thread->image_url }}')">
                        @endif
                    </div>
                @endif

                <div class="d-flex gap-4 border-top pt-3">
                    <button type="button"
                        class="btn btn-link text-decoration-none p-0 d-flex gap-1 interaction-btn {{ auth()->check() && $thread->likes()->where('user_id', auth()->id())->exists() ? 'text-danger' : 'text-muted' }}"
                        @auth onclick="Forum.toggleLike(this, 'thread', '{{ $thread->uuid }}')" @else
                        onclick="Forum.guestAction()" @endauth>
                        <span class="material-icons fs-5">
                            {{ auth()->check() && $thread->likes()->where('user_id', auth()->id())->exists() ? 'favorite' : 'favorite_border' }}
                        </span>
                        <span class="small count fw-bold">{{ $thread->likes_count }}</span>
                    </button>
                    <button class="btn btn-link text-muted p-0 d-flex gap-1 text-decoration-none interaction-btn" @auth
                    onclick="document.getElementById('mainReplyInput').focus()" @else onclick="Forum.guestAction()"
                        @endauth>
                        <span class="material-icons fs-5">chat_bubble_outline</span>
                        <span class="small fw-bold">{{ $thread->posts_count }}</span>
                    </button>
                    <button class="btn btn-link text-muted p-0 d-flex gap-1 text-decoration-none interaction-btn"
                        onclick="Forum.openShareModal('{{ route('threads.show', $thread->uuid) }}', '{{ $thread->title ?? 'Thread dari ' . $thread->user->username }}')">
                        <span class="material-icons fs-5">share</span>
                    </button>
                </div>
            </div>

            <!-- Reply Form -->
            @auth
                <div class="card shadow-sm rounded-4 border-0 p-3 mb-4">
                    <form id="mainReplyForm">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center flex-shrink-0"
                                style="width: 40px; height: 40px; overflow:hidden;">
                                <img src="{{ auth()->user()->avatar_url }}" alt="Me" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="flex-grow-1">
                                <textarea id="mainReplyInput" class="form-control border-0 bg-light rounded-3" rows="1"
                                    placeholder="Balas thread ini..." required style="resize:none; padding: 10px;"
                                    maxlength="256"></textarea>
                                <div class="text-end mt-1 d-none" id="mainReplyCounter">
                                    <small class="text-muted"><span id="mainReplyCount">0</span>/256</small>
                                </div>
                            </div>
                            <button type="submit"
                                class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <span class="material-icons fs-5">send</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endauth

            <!-- Replies -->
            <div class="d-flex flex-column gap-3 mb-5">
                @auth
                    @php
                        // Get root posts (parent_id is null) ordered by pinned status then creation date
                        $posts = $thread->posts()
                            ->with([
                                'user',
                                'replies' => function ($q) {
                                    $q->with('user')->withCount('likes');
                                }
                            ])
                            ->withCount('likes')
                            ->whereNull('parent_id')
                            ->orderByDesc('is_pinned')
                            ->latest()
                            ->get();
                    @endphp

                    @forelse($posts as $post)
                        @include('threads.partials.comment', ['post' => $post])
                    @empty
                        <div class="text-center py-4 text-muted">
                            <small>Belum ada balasan. Jadilah yang pertama!</small>
                        </div>
                    @endforelse
                @else
                    <div class="text-center py-5">
                        <span class="material-icons fs-1 text-muted mb-2">lock</span>
                        <h5 class="fw-bold text-dark">Konten Terkunci</h5>
                        <p class="text-muted mb-3">Login untuk melihat, menyukai, dan membalas komentar.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4">Login Sekarang</a>
                    </div>
                @endauth
            </div>
        </div>

        @if($thread->space)
            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <!-- Space Header mini -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="p-4 bg-sage-linear text-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white rounded-3 p-2 d-flex align-items-center justify-content-center text-primary"
                                style="width: 42px; height: 42px;">
                                <span class="material-icons">groups</span>
                            </div>
                            <div>
                                <h6 class="fw-black mb-0">{{ $thread->space->name }}</h6>
                                <div class="xs-text opacity-75">{{ $thread->space->members->count() }} Anggota</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-2">Tentang Space</h6>
                        <p class="text-muted small mb-3 lh-lg">{{ $thread->space->description }}</p>

                        <a href="{{ route('spaces.show', $thread->space->slug) }}"
                            class="btn btn-primary w-100 rounded-pill fw-bold small py-2">Lihat Selengkapnya</a>
                    </div>
                </div>

                <!-- Admin Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3">Pengurus</h6>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <img src="{{ $thread->space->owner->avatar_url }}" class="rounded-circle object-fit-cover"
                                style="width: 32px; height: 32px;">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark text-sm">{{ $thread->space->owner->name }}</span>
                                <span class="text-muted xs-text">Pemilik</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Include Modals (reuse structure from home) --}}
    @include('layouts.partials.modals')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const mainReplyForm = document.getElementById('mainReplyForm');
                const mainReplyInput = document.getElementById('mainReplyInput');
                const counterDiv = document.getElementById('mainReplyCounter');
                const countSpan = document.getElementById('mainReplyCount');

                if (mainReplyInput) {
                    mainReplyInput.addEventListener('focus', () => counterDiv.classList.remove('d-none'));
                    mainReplyInput.addEventListener('blur', () => {
                        if (mainReplyInput.value.length === 0) counterDiv.classList.add('d-none');
                    });
                    mainReplyInput.addEventListener('input', () => {
                        countSpan.textContent = mainReplyInput.value.length;
                    });
                }

                if (mainReplyForm) {
                    mainReplyForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const btn = mainReplyForm.querySelector('button');
                        btn.disabled = true;

                        const result = await Forum.submitComment('{{ $thread->uuid }}', mainReplyInput.value);
                        if (result && result.success) {
                            Forum.showToast('Balasan terkirim!');
                            setTimeout(() => location.reload(), 500);
                        }
                        btn.disabled = false;
                    });
                }
            });
        </script>
    @endpush
@endsection