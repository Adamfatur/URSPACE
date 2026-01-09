@extends('layouts.app')

@section('title', 'Thread Tersimpan - Forum UR')
@section('meta_description', 'Daftar thread yang telah Anda simpan di Forum UR')
@section('canonical', route('bookmarks.index'))

@section('container_width', '680px')

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            <span class="material-icons align-middle me-2">bookmark</span>
            Thread Tersimpan
        </h4>
        <p class="text-muted mb-0">Thread yang Anda simpan untuk dibaca nanti</p>
    </div>

    <div class="d-flex flex-column gap-3">
        @forelse($bookmarks as $bookmark)
            @if($bookmark->thread)
                @include('threads.partials.thread_card', ['thread' => $bookmark->thread])
            @endif
        @empty
            <div class="text-center py-5 card rounded-4 border-0 shadow-sm">
                <span class="material-icons text-muted opacity-50 mb-3" style="font-size: 64px;">bookmark_border</span>
                <h5 class="fw-bold text-muted">Belum ada thread tersimpan</h5>
                <p class="text-muted small">Klik ikon bookmark pada thread untuk menyimpannya di sini.</p>
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill btn-sm mx-auto mt-2">
                    Jelajahi Thread
                </a>
            </div>
        @endforelse
    </div>

    @if($bookmarks->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $bookmarks->links() }}
        </div>
    @endif
@endsection