@php
    $isOwner = auth()->check() && auth()->id() == $post->thread->user_id;
    $isPostOwner = auth()->check() && auth()->id() == $post->user_id;
@endphp

<div class="d-flex gap-3 mb-3 p-3 rounded-4 shadow-sm border {{ $post->is_pinned ? 'border-primary' : 'bg-white border-0' }}"
    style="{{ $post->is_pinned ? 'background-color: #f0f9f0 !important;' : '' }}" id="post-{{ $post->id }}">
    <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center flex-shrink-0"
        style="width: 40px; height: 40px; overflow:hidden;">
        <img src="{{ $post->user->avatar_url }}" alt="User" class="w-100 h-100 object-fit-cover">
    </div>
    <div class="flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold">{{ $post->user->username }}</span>
                @if($post->user_id == $post->thread->user_id)
                    <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.7rem;">OP</span>
                @endif
                <span class="text-muted small">• {{ $post->created_at->diffForHumans() }}</span>
                @if($post->is_pinned)
                    <span class="badge bg-primary text-white d-flex align-items-center gap-1 shadow-sm px-2">
                        <span class="material-icons" style="font-size: 12px;">push_pin</span>
                        Pinned
                    </span>
                @endif
            </div>

            <div class="dropdown">
                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                    <span class="material-icons">more_horiz</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4">
                    @if($isOwner)
                        <li>
                            <button type="button"
                                class="dropdown-item d-flex align-items-center justify-content-start gap-2"
                                onclick="Forum.togglePin(this, {{ $post->id }})">
                                <span class="material-icons small">{{ $post->is_pinned ? 'push_pin' : 'push_pin' }}</span>
                                <span>{{ $post->is_pinned ? 'Unpin Komentar' : 'Pin Komentar' }}</span>
                            </button>
                        </li>
                        <li>
                            <button type="button"
                                class="dropdown-item d-flex align-items-center justify-content-start gap-2"
                                onclick="Forum.toggleHide(this, {{ $post->id }})">
                                <span
                                    class="material-icons small">{{ $post->status == 'hidden' ? 'visibility' : 'visibility_off' }}</span>
                                <span>{{ $post->status == 'hidden' ? 'Tampilkan' : 'Sembunyikan' }}</span>
                            </button>
                        </li>
                    @endif
                    <li>
                        <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
                            data-bs-target="#reportModal" data-type="post" data-id="{{ $post->id }}">
                            <span class="material-icons small">flag</span> Laporkan
                        </button>
                    </li>
                    @if($isOwner || $isPostOwner)
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <button class="dropdown-item text-danger d-flex align-items-center gap-2"
                                onclick="Forum.deletePost({{ $post->id }})">
                                <span class="material-icons small">delete</span> Hapus
                            </button>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mb-2 {{ $post->status == 'hidden' ? 'text-muted fst-italic' : 'text-dark w-100' }}"
            style="word-wrap: break-word;">
            @if($post->status == 'hidden')
                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded-3">
                    <span class="material-icons text-muted">visibility_off</span>
                    <span>Komentar ini disembunyikan oleh pemilik thread.</span>
                </div>
                @if($isOwner || $isPostOwner)
                    <div class="mt-1 small text-muted border-start ps-2">
                        {{ $post->content }}
                    </div>
                @endif
            @else
                <div class="markdown-content" style="line-height: 1.5; font-size: 0.95rem;">
                    {!! Str::markdown($post->content, ['html_input' => 'escape']) !!}
                </div>
            @endif
        </div>

        <div class="d-flex gap-3">
            <button class="btn btn-link text-muted p-0 text-decoration-none d-flex align-items-center gap-1"
                onclick="Forum.toggleLike(this, 'post', {{ $post->id }})">
                <span
                    class="material-icons fs-6 {{ $post->likes()->where('user_id', auth()->id())->exists() ? 'text-danger' : '' }}">
                    {{ $post->likes()->where('user_id', auth()->id())->exists() ? 'favorite' : 'favorite_border' }}
                </span>
                <span class="count small fw-bold">{{ $post->likes_count }}</span>
            </button>
            <button class="btn btn-link text-muted p-0 text-decoration-none d-flex align-items-center gap-1"
                data-bs-toggle="modal" data-bs-target="#commentModal" data-thread-id="{{ $post->thread->uuid }}"
                data-parent-id="{{ $post->id }}" data-thread-username="{{ $post->user->username }}">
                <span class="material-icons fs-6">chat_bubble_outline</span>
                <span class="small fw-bold">Balas</span>
            </button>
        </div>

        @if($post->replies->count() > 0)
            <div class="mt-3 ps-3 border-start">
                @foreach($post->replies as $reply)
                    @include('threads.partials.comment', ['post' => $reply])
                @endforeach
            </div>
        @endif
    </div>
</div>