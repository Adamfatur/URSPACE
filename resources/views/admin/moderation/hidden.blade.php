@extends('layouts.admin')

@section('title', 'Hidden Content')

@section('content')
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Moderated Threads</h5>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">
                {{ $hiddenThreads->total() }} Hidden
            </span>
        </div>

        @if(session('success'))
            <div class="px-4 pt-3">
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-icons">check_circle</span>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="card-body p-0 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted small text-uppercase">Thread Info</th>
                            <th class="px-4 py-3 text-muted small text-uppercase">Author</th>
                            <th class="px-4 py-3 text-muted small text-uppercase">Hidden Date</th>
                            <th class="px-4 py-3 text-muted small text-uppercase text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hiddenThreads as $thread)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-column gap-1">
                                        <a href="{{ route('threads.show', $thread) }}"
                                            class="text-decoration-none text-dark fw-bold text-truncate d-block"
                                            style="max-width: 350px;">
                                            {{ $thread->title ?? Str::limit($thread->content, 50) }}
                                        </a>
                                        <div class="d-flex gap-2">
                                            <span
                                                class="badge bg-light text-muted border rounded-pill fw-normal">{{ $thread->category->name ?? 'No Category' }}</span>
                                            <span
                                                class="badge bg-light text-muted border rounded-pill fw-normal">{{ ucfirst($thread->type) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $thread->user->avatar_url }}" class="rounded-circle object-fit-cover shadow-sm"
                                            width="40" height="40">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold small text-dark">{{ $thread->user->name }}</span>
                                            <span class="text-muted" style="font-size: 0.75rem;">@
                                                {{ $thread->user->username }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold small text-dark">{{ $thread->updated_at->format('M d, Y') }}</span>
                                        <span class="text-muted small">{{ $thread->updated_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('admin.threads.hide', $thread) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-success rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm"
                                                title="Restore Thread">
                                                <span class="material-icons" style="font-size: 16px;">visibility</span>
                                                <span>Restore</span>
                                            </button>
                                        </form>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmPermanentDeleteModal{{ $thread->id }}"
                                            title="Delete Permanently">
                                            <span class="material-icons" style="font-size: 16px;">delete</span>
                                            <span>Delete</span>
                                        </button>

                                        {{-- Permanent Delete Modal --}}
                                        <div class="modal fade" id="confirmPermanentDeleteModal{{ $thread->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <div class="modal-body text-center p-4">
                                                        <div class="text-danger mb-3">
                                                            <span class="material-icons"
                                                                style="font-size: 48px;">warning_amber</span>
                                                        </div>
                                                        <h5 class="fw-bold text-danger">Hapus Permanen?</h5>
                                                        <p class="text-muted small">Thread ini akan dihapus selamanya dari
                                                            database.</p>
                                                        <div class="d-grid gap-2 mt-4">
                                                            <form action="{{ route('admin.threads.destroy', $thread) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger rounded-pill fw-bold w-100">Ya, Hapus
                                                                    Selamanya</button>
                                                            </form>
                                                            <button type="button" class="btn btn-light rounded-pill"
                                                                data-bs-dismiss="modal">Batal</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center py-4">
                                        <div class="bg-success-subtle text-success rounded-circle p-4 mb-3 d-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <span class="material-icons display-6">check_circle</span>
                                        </div>
                                        <h5 class="fw-bold text-dark">All Clear!</h5>
                                        <p class="text-muted small mb-0">No hidden content found. Everything is visible to the
                                            public.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hiddenThreads->hasPages())
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                    {{ $hiddenThreads->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection