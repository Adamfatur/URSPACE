@extends('layouts.app')

@section('container_width', '900px')

@section('content')
    {{-- Profile Header Card --}}
    <div class="card rounded-4 border-0 shadow-sm overflow-hidden mb-4">
        {{-- Cover Banner --}}
        <div class="profile-cover" style="height: 160px; background: linear-gradient(135deg, #5e8b5e 0%, #4a6f4a 100%);">
        </div>

        <div class="card-body p-4 pt-0">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3 gap-md-4"
                style="margin-top: -50px;">
                {{-- Avatar --}}
                <div class="position-relative flex-shrink-0">
                    <div class="rounded-circle border border-4 border-white shadow-lg overflow-hidden bg-white"
                        style="width: 110px; height: 110px;">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                            class="w-100 h-100 object-fit-cover">
                    </div>
                    @if($user->is_open_to_work)
                        <span
                            class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-2 border-white shadow-sm px-2 py-1"
                            style="font-size: 0.65rem;">
                            <span class="material-icons align-middle" style="font-size: 12px;">work</span>
                        </span>
                    @endif
                </div>

                {{-- Name & Actions --}}
                <div class="flex-grow-1 text-center text-md-start">
                    <div
                        class="d-flex flex-column flex-md-row align-items-center align-items-md-start justify-content-between gap-2">
                        <div>
                            <h4 class="fw-bold mb-0" style="color: #1f2c1f;">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">{{ $user->username }}</p>
                        </div>
                        <div class="d-flex gap-2 mt-2 mt-md-0">
                            @if(auth()->id() == $user->id)
                                <a href="{{ route('profile.edit') }}"
                                    class="btn btn-outline-dark rounded-pill fw-semibold px-4">
                                    <span class="material-icons align-middle me-1" style="font-size: 18px;">edit</span>Edit
                                    Profil
                                </a>
                            @elseif(auth()->check())
                                <form action="{{ route('profile.follow', $user) }}" method="POST">
                                    @csrf
                                    @if(auth()->user()->following()->where('followed_id', $user->id)->exists())
                                        <button type="submit" class="btn btn-outline-secondary rounded-pill fw-semibold px-4">
                                            <span class="material-icons align-middle me-1"
                                                style="font-size: 18px;">check</span>Mengikuti
                                        </button>
                                        @method('DELETE')
                                    @else
                                        <button type="submit" class="btn btn-primary rounded-pill fw-semibold px-4 shadow-sm">
                                            <span class="material-icons align-middle me-1"
                                                style="font-size: 18px;">person_add</span>Ikuti
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats & Bio --}}
            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-7">
                    @if($user->headline)
                        <p class="mb-2 fw-medium" style="color: #395539;">{{ $user->headline }}</p>
                    @endif
                    @if($user->bio)
                        <p class="text-muted mb-3">{{ $user->bio }}</p>
                    @endif
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                        @if($user->location)
                            <span class="d-flex align-items-center gap-1">
                                <span class="material-icons" style="font-size: 16px;">location_on</span>{{ $user->location }}
                            </span>
                        @endif
                        @if($user->program_studi)
                            <span class="d-flex align-items-center gap-1">
                                <span class="material-icons" style="font-size: 16px;">school</span>{{ $user->program_studi }}
                            </span>
                        @endif
                        @if($user->angkatan)
                            <span class="d-flex align-items-center gap-1">
                                <span class="material-icons" style="font-size: 16px;">event</span>Angkatan {{ $user->angkatan }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-5 mt-3 mt-md-0">
                    <div class="d-flex justify-content-center justify-content-md-end gap-4">
                        <div class="text-center">
                            <div class="fw-bold fs-5" style="color: #1f2c1f;">{{ $user->followers()->count() }}</div>
                            <div class="text-muted small">Pengikut</div>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold fs-5" style="color: #1f2c1f;">{{ $user->following()->count() }}</div>
                            <div class="text-muted small">Mengikuti</div>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold fs-5" style="color: #1f2c1f;">{{ $user->threads()->count() }}</div>
                            <div class="text-muted small">Thread</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social Links & Skills Compact (Login Only) --}}
            @auth
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 pt-3 border-top">
                    <div class="d-flex flex-wrap gap-2">
                        @if($user->skills->count() > 0)
                            @foreach($user->skills->take(5) as $skill)
                                <span class="badge rounded-pill px-3 py-2 fw-normal"
                                    style="background-color: #e3ebe3; color: #395539;">{{ $skill->name }}</span>
                            @endforeach
                            @if($user->skills->count() > 5)
                                <span class="badge rounded-pill px-3 py-2 fw-normal text-muted"
                                    style="background-color: #f4f7f4;">+{{ $user->skills->count() - 5 }} lainnya</span>
                            @endif
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        @if($user->website)
                            <a href="{{ $user->website }}" target="_blank" class="btn btn-sm btn-light rounded-circle p-2"
                                title="Website">
                                <span class="material-icons" style="font-size: 18px; color: #5e8b5e;">language</span>
                            </a>
                        @endif
                        @if($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" target="_blank" class="btn btn-sm btn-light rounded-circle p-2"
                                title="LinkedIn">
                                <span class="material-icons" style="font-size: 18px; color: #0077b5;">link</span>
                            </a>
                        @endif
                        @if($user->github_url)
                            <a href="{{ $user->github_url }}" target="_blank" class="btn btn-sm btn-light rounded-circle p-2"
                                title="GitHub">
                                <span class="material-icons" style="font-size: 18px; color: #333;">code</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- Main Content: Tabs & Activity --}}
    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Activity Tabs --}}
            <div class="trending-tabs mb-3">
                <a href="{{ route('profile.show', ['user' => $user->username, 'tab' => 'threads']) }}"
                    class="tab-item {{ $tab === 'threads' ? 'active' : '' }}">Threads</a>
                <a href="{{ route('profile.show', ['user' => $user->username, 'tab' => 'replies']) }}"
                    class="tab-item {{ $tab === 'replies' ? 'active' : '' }}">Balasan</a>
                <a href="{{ route('profile.show', ['user' => $user->username, 'tab' => 'likes']) }}"
                    class="tab-item {{ $tab === 'likes' ? 'active' : '' }}">Disukai</a>
            </div>

            {{-- Activity Feed --}}
            @forelse($content as $item)
                @if($tab === 'replies')
                    <div class="card rounded-4 border-0 shadow-sm mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start gap-3">
                                <span class="material-icons" style="color: #a3c0a3;">reply</span>
                                <div>
                                    <p class="text-muted small mb-1">
                                        Membalas thread <a href="{{ route('threads.show', $item->thread) }}"
                                            class="fw-bold text-decoration-none hover-primary"
                                            style="color: #395539;">{{ $item->thread->title }}</a>
                                    </p>
                                    <p class="mb-2" style="color: #1f2c1f;">{{ $item->content }}</p>
                                    <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @include('threads.partials.thread_card', [
                        'thread' => $item,
                        'compact' => true,
                        'hideAvatar' => true,
                        'showMedia' => true
                    ])
                @endif
            @empty
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <span class="material-icons mb-2" style="font-size: 48px; color: #c5d8c5;">inbox</span>
                        <p class="text-muted mb-0">Belum ada aktivitas di tab ini.</p>
                    </div>
                </div>
            @endforelse

            @if($content->hasPages())
                <div class="mt-4">{{ $content->links() }}</div>
            @endif
        </div>

        {{-- Sidebar: Career & Education (Login Only) --}}
        <div class="col-lg-4">
            @auth
                @if($user->experiences->count() > 0)
                    <div class="card rounded-4 border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #395539;">
                                <span class="material-icons" style="font-size: 20px;">work</span> Pengalaman
                            </h6>
                            @foreach($user->experiences as $exp)
                                <div class="d-flex gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 40px; height: 40px; background-color: #e3ebe3;">
                                        <span class="material-icons" style="font-size: 20px; color: #5e8b5e;">business</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: #1f2c1f;">{{ $exp->title }}</div>
                                        <div class="text-muted small">{{ $exp->company }}</div>
                                        <div class="text-muted small">{{ $exp->start_date->format('M Y') }} -
                                            {{ $exp->is_current ? 'Sekarang' : $exp->end_date?->format('M Y') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            @if($user->educations->count() > 0)
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #395539;">
                            <span class="material-icons" style="font-size: 20px;">school</span> Pendidikan
                        </h6>
                        @foreach($user->educations as $edu)
                            <div class="d-flex gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 40px; height: 40px; background-color: #e3ebe3;">
                                    <span class="material-icons" style="font-size: 20px; color: #5e8b5e;">account_balance</span>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="color: #1f2c1f;">{{ $edu->institution }}</div>
                                    <div class="text-muted small">
                                        {{ $edu->degree }}{{ $edu->field_of_study ? ' - ' . $edu->field_of_study : '' }}</div>
                                    <div class="text-muted small">{{ $edu->start_year }} -
                                        {{ $edu->is_current ? 'Sekarang' : $edu->end_year }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($user->certifications->count() > 0)
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #395539;">
                            <span class="material-icons" style="font-size: 20px;">verified</span> Sertifikasi
                        </h6>
                        @foreach($user->certifications as $cert)
                            <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'mb-3' : '' }}">
                                <span class="material-icons" style="color: #ffc107;">workspace_premium</span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small" style="color: #1f2c1f;">{{ $cert->name }}</div>
                                    <div class="text-muted small">{{ $cert->issuer }}</div>
                                </div>
                                @if($cert->credential_url)
                                    <a href="{{ $cert->credential_url }}" target="_blank" class="text-muted"><span
                                            class="material-icons" style="font-size: 18px;">open_in_new</span></a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Open to Work Banner --}}
                @if($user->is_open_to_work)
                    <div class="card rounded-4 border-0 shadow-sm overflow-hidden"
                        style="background: linear-gradient(135deg, #e3ebe3 0%, #c5d8c5 100%);">
                        <div class="card-body p-4 text-center">
                            <span class="material-icons mb-2" style="font-size: 32px; color: #4a6f4a;">work_outline</span>
                            <h6 class="fw-bold mb-1" style="color: #395539;">Terbuka untuk Peluang</h6>
                            <p class="text-muted small mb-0">{{ $user->name }} sedang mencari peluang baru.</p>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>
@endsection