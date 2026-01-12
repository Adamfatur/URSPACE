<div class="p-4 pt-5">
    @if($event->cover_image)
        <div class="ratio ratio-21x9 overflow-hidden rounded-4 mb-4 shadow-sm">
            <img src="{{ asset('storage/' . $event->cover_image) }}" class="object-fit-cover w-100 h-100"
                alt="{{ $event->title }}">
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
        <div>
            <h2 class="fw-black text-dark mb-1 fs-3">{{ $event->title }}</h2>
            <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                <div class="d-flex align-items-center gap-1">
                    <span class="material-icons small fs-6">person</span>
                    <span>Dibuat oleh <strong class="text-dark">{{ $event->creator->name }}</strong></span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="material-icons small fs-6">event</span>
                    <span>{{ $event->starts_at->translatedFormat('d F Y') }}</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="material-icons small fs-6">schedule</span>
                    <span>{{ $event->starts_at->format('H:i') }} WIB</span>
                </div>
                @if($event->location_detail)
                    <div class="d-flex align-items-center gap-1">
                        <span class="material-icons small fs-6">
                            {{ $event->location_type === 'online' ? 'videocam' : ($event->location_type === 'hybrid' ? 'sync_alt' : 'place') }}
                        </span>
                        <span>{{ $event->location_detail }}</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="text-end mt-md-3 d-flex align-items-center gap-2">
            @if(!$space->is_private && $event->visibility === 'open')
                <button onclick="openShareModal()" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1 shadow-xs">
                    <span class="material-icons" style="font-size: 18px;">share</span>
                    <span class="d-none d-sm-inline">Bagikan</span>
                </button>
            @endif
            <div
                class="badge {{ $event->isUpcoming() ? 'bg-primary-subtle text-primary' : ($event->isOngoing() ? 'bg-success-subtle text-success' : 'bg-light text-muted border') }} rounded-pill px-3 py-2 fw-bold">
                {{ $event->isUpcoming() ? 'Akan Datang' : ($event->isOngoing() ? 'Sedang Berlangsung' : 'Selesai') }}
            </div>
        </div>
    </div>

    <script>
        function openShareModal() {
            const modal = new bootstrap.Modal(document.getElementById('shareEventModal'));
            modal.show();
        }

        async function confirmShare() {
            // Close modal
            const modalEl = document.getElementById('shareEventModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            try {
                const response = await fetch("{{ route('spaces.events.share', ['space' => $space->slug, 'event' => $event->uuid]) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (window.Forum && window.Forum.showToast) {
                        window.Forum.showToast(data.message, 'success');
                    } else {
                        alert(data.message); // Fallback
                    }
                } else {
                    if (window.Forum && window.Forum.showToast) {
                        window.Forum.showToast(data.message, 'danger');
                    } else {
                        alert(data.message);
                    }
                }
            } catch (error) {
                console.error('Share error:', error);
                alert('Gagal membagikan acara.');
            }
        }
    </script>

    {{-- Share Confirmation Modal --}}
    <div class="modal fade" id="shareEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <span class="material-icons fs-1">share</span>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2">Bagikan Acara Ini?</h5>
                    <p class="text-muted mb-4">Acara <strong>{{ $event->title }}</strong> akan dibagikan ke timeline Anda sebagai thread baru.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="confirmShare()">Ya, Bagikan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 bg-white rounded-4 shadow-sm p-4">
                <h6 class="fw-bold mb-3 text-uppercase x-small text-muted tracking-widest">Deskripsi Acara</h6>
                <div class="text-secondary lh-lg fs-6" style="text-align: justify;">
                    {!! nl2br(e($event->description)) !!}
                </div>

                @if($event->ends_at)
                    <div class="mt-4 pt-3 border-top d-flex align-items-center gap-3 text-muted">
                        <span class="material-icons opacity-50">event_repeat</span>
                        <span class="small fw-medium">Hingga {{ $event->ends_at->translatedFormat('d F Y, H:i') }}
                            WIB</span>
                    </div>
                @endif
            </div>

            {{-- Event Announcements Section --}}
            <div class="card border-0 bg-white rounded-4 shadow-sm p-4 mt-4">
                <h6
                    class="fw-bold mb-3 text-uppercase x-small text-muted tracking-widest d-flex align-items-center gap-2">
                    <span class="material-icons small">campaign</span> Update Acara
                </h6>

                {{-- Post Announcement Form (Owner Only) --}}
                @if(auth()->id() == $event->created_by)
                    <form
                        action="{{ route('spaces.events.announcements.store', ['space' => $event->space->slug, 'event' => $event->uuid]) }}"
                        method="POST" class="mb-4">
                        @csrf
                        <textarea name="content" class="form-control rounded-3 bg-light border-0 mb-2" rows="3"
                            placeholder="Tulis update atau informasi baru untuk peserta..." required></textarea>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <span class="material-icons small align-middle me-1">send</span> Kirim Update
                        </button>
                    </form>
                @endif

                {{-- Announcements List --}}
                <div class="d-flex flex-column gap-3">
                    @forelse($event->announcements as $announcement)
                        <div class="p-3 rounded-3 bg-light border-start border-primary border-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 28px; height: 28px; overflow:hidden;">
                                    <img src="{{ $announcement->user->avatar_url }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div>
                                    <span class="fw-bold small text-dark">{{ $announcement->user->name }}</span>
                                    <span class="text-muted x-small">•
                                        {{ $announcement->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="text-dark small lh-lg">{!! nl2br(e($announcement->content)) !!}</div>
                        </div>
                    @empty
                        <p class="text-muted small text-center py-3 mb-0">Belum ada update untuk acara ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Event Voting Section --}}
            <div class="card border-0 bg-white rounded-4 shadow-sm p-4 mt-4">
                <h6
                    class="fw-bold mb-3 text-uppercase x-small text-muted tracking-widest d-flex align-items-center gap-2">
                    <span class="material-icons small">how_to_vote</span> Voting & Polling
                </h6>

                {{-- Create Vote Form (Owner Only) --}}
                @if(auth()->id() == $event->created_by)
                    <button class="btn btn-outline-primary btn-sm rounded-pill mb-3" data-bs-toggle="modal"
                        data-bs-target="#createVoteModal">
                        <span class="material-icons small align-middle me-1">add</span> Buat Voting Baru
                    </button>
                @endif

                {{-- Votes List --}}
                <div class="d-flex flex-column gap-3">
                    @forelse($event->votes as $vote)
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $vote->title }}</h6>
                                    @if($vote->description)
                                        <p class="text-muted small mb-2">{{ $vote->description }}</p>
                                    @endif
                                </div>
                                @if($vote->is_anonymous)
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill small">Anonim</span>
                                @endif
                            </div>

                            @php
                                $totalResponses = $vote->responses->count();
                                $hasVoted = $vote->hasUserVoted(auth()->id());
                                $userVote = $hasVoted ? $vote->getUserVote(auth()->id()) : null;
                            @endphp

                            @if($hasVoted || ($vote->ends_at && $vote->ends_at->isPast()))
                                {{-- Show Results --}}
                                <div class="d-flex flex-column gap-2">
                                    @foreach($vote->options as $option)
                                        @php
                                            $optionCount = $option->responses->count();
                                            $percentage = $totalResponses > 0 ? round(($optionCount / $totalResponses) * 100) : 0;
                                            $isSelected = $userVote && $userVote->option_id == $option->id;
                                        @endphp
                                        <div class="position-relative rounded-3 overflow-hidden" style="background: #f0f0f0;">
                                            {{-- Progress bar background --}}
                                            <div class="position-absolute top-0 start-0 h-100 rounded-3 {{ $isSelected ? 'bg-success' : 'bg-primary' }}" 
                                                style="width: {{ $percentage }}%; opacity: 0.2;"></div>
                                            {{-- Content --}}
                                            <div class="d-flex justify-content-between align-items-center px-3 py-2 position-relative">
                                                <span class="fw-medium small {{ $isSelected ? 'text-success' : 'text-dark' }} d-flex align-items-center gap-1">
                                                    @if($isSelected)
                                                        <span class="material-icons small">check_circle</span>
                                                    @endif
                                                    {{ $option->option_text }}
                                                </span>
                                                <span class="fw-bold small {{ $isSelected ? 'text-success' : 'text-dark' }}">{{ $percentage }}%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                    <p class="text-muted x-small mb-0 mt-1">Total {{ $totalResponses }} suara</p>
                                </div>

                            @else
                                {{-- Show Voting Options --}}
                                <form
                                    action="{{ route('spaces.events.votes.cast', ['space' => $event->space->slug, 'event' => $event->uuid, 'vote' => $vote->uuid]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="d-flex flex-column gap-2 mb-2">
                                        @foreach($vote->options as $option)
                                            <div class="form-check p-0">
                                                <input class="btn-check" type="radio" name="option_id" id="option_{{ $option->id }}"
                                                    value="{{ $option->id }}" required>
                                                <label class="btn btn-outline-primary w-100 text-start rounded-3"
                                                    for="option_{{ $option->id }}">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                                        <span class="material-icons small align-middle me-1">how_to_vote</span> Vote
                                    </button>
                                </form>
                            @endif

                            @if($vote->ends_at)
                                <p class="text-muted x-small mb-0 mt-2">
                                    <span class="material-icons x-small align-middle">schedule</span>
                                    {{ $vote->ends_at->isPast() ? 'Berakhir' : 'Berakhir' }}
                                    {{ $vote->ends_at->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small text-center py-3 mb-0">Belum ada voting untuk acara ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Create Vote Modal --}}
            @if(auth()->id() == $event->created_by)
                <div class="modal fade" id="createVoteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Buat Voting Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form
                                action="{{ route('spaces.events.votes.store', ['space' => $event->space->slug, 'event' => $event->uuid]) }}"
                                method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Judul Voting</label>
                                        <input type="text" name="title" class="form-control rounded-3 bg-light border-0"
                                            placeholder="Misal: Pemilihan Ketua" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Deskripsi (Opsional)</label>
                                        <textarea name="description" class="form-control rounded-3 bg-light border-0"
                                            rows="2" placeholder="Jelaskan tujuan voting..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Opsi Pilihan</label>
                                        <div id="voteOptionsContainer">
                                            <input type="text" name="options[]"
                                                class="form-control rounded-3 bg-light border-0 mb-2" placeholder="Opsi 1"
                                                required>
                                            <input type="text" name="options[]"
                                                class="form-control rounded-3 bg-light border-0 mb-2" placeholder="Opsi 2"
                                                required>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                                            onclick="addVoteOption()">
                                            <span class="material-icons small align-middle">add</span> Tambah Opsi
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Batas Waktu (Opsional)</label>
                                        <input type="datetime-local" name="ends_at"
                                            class="form-control rounded-3 bg-light border-0">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_anonymous" id="isAnonymous"
                                            value="1">
                                        <label class="form-check-label small" for="isAnonymous">Voting Anonim (Hasil tidak
                                            menunjukkan siapa memilih apa)</label>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Buat Voting</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    function addVoteOption() {
                        const container = document.getElementById('voteOptionsContainer');
                        const count = container.querySelectorAll('input').length + 1;
                        if (count <= 10) {
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.name = 'options[]';
                            input.className = 'form-control rounded-3 bg-light border-0 mb-2';
                            input.placeholder = `Opsi ${count}`;
                            input.required = true;
                            container.appendChild(input);
                        }
                    }
                </script>
            @endif

            {{-- Tournament Bracket Section --}}
            <div class="card border-0 bg-white rounded-4 shadow-sm p-4 mt-4">
                <h6
                    class="fw-bold mb-3 text-uppercase x-small text-muted tracking-widest d-flex align-items-center gap-2">
                    <span class="material-icons small">emoji_events</span> Bracket Lomba
                </h6>

                {{-- Create Bracket Button (Owner Only) --}}
                @if(auth()->id() == $event->created_by)
                    <button class="btn btn-outline-primary btn-sm rounded-pill mb-3" data-bs-toggle="modal"
                        data-bs-target="#createBracketModal">
                        <span class="material-icons small align-middle me-1">add</span> Buat Bracket Baru
                    </button>
                @endif

                {{-- Brackets List --}}
                <div class="d-flex flex-column gap-4">
                    @forelse($event->brackets as $bracket)
                        <div class="p-3 rounded-3 border bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $bracket->title }}</h6>
                                    <span
                                        class="badge {{ $bracket->status === 'registration' ? 'bg-info-subtle text-info' : ($bracket->status === 'ongoing' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success') }} rounded-pill">
                                        {{ $bracket->status === 'registration' ? 'Pendaftaran' : ($bracket->status === 'ongoing' ? 'Berjalan' : 'Selesai') }}
                                    </span>
                                </div>
                                <span
                                    class="text-muted small">{{ $bracket->participants->count() }}/{{ $bracket->max_participants }}
                                    Peserta</span>
                            </div>

                            {{-- Participants --}}
                            @if($bracket->status === 'registration')
                                <div class="mb-3">
                                    <p class="small text-muted mb-2">Peserta Terdaftar:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($bracket->participants as $participant)
                                            <span
                                                class="badge bg-white border text-dark rounded-pill px-2 py-1">{{ $participant->display_name }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                @if(auth()->id() == $event->created_by)
                                    {{-- Add Participant Form --}}
                                    <form
                                        action="{{ route('spaces.events.brackets.participants.add', ['space' => $event->space->slug, 'event' => $event->uuid, 'bracket' => $bracket->uuid]) }}"
                                        method="POST" class="d-flex gap-2 mb-3">
                                        @csrf
                                        <input type="text" name="name"
                                            class="form-control form-control-sm rounded-pill bg-white border"
                                            placeholder="Nama peserta" required>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Tambah</button>
                                    </form>

                                    @if($bracket->participants->count() >= 2)
                                        <form
                                            action="{{ route('spaces.events.brackets.generate', ['space' => $event->space->slug, 'event' => $event->uuid, 'bracket' => $bracket->uuid]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill">
                                                <span class="material-icons small align-middle me-1">play_arrow</span> Mulai Bracket
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                {{-- Bracket Visualization --}}
                                <div class="bracket-container overflow-auto pb-3">
                                    <div class="d-flex gap-5 align-items-start">
                                        @php
                                            $maxRound = $bracket->matches->max('round');
                                        @endphp
                                        @for($round = 1; $round <= $maxRound; $round++)
                                            <div class="bracket-round flex-shrink-0" style="min-width: 220px;">
                                                {{-- Round Header --}}
                                                <div class="text-center mb-3">
                                                    @if($round == $maxRound)
                                                        <span class="material-icons text-warning mb-1" style="font-size: 28px;">emoji_events</span>
                                                        <p class="fw-bold text-dark mb-0">Final</p>
                                                    @elseif($round == $maxRound - 1)
                                                        <p class="fw-bold text-muted mb-0 py-2">Semi Final</p>
                                                    @else
                                                        <p class="fw-bold text-muted mb-0 py-2">Round {{ $round }}</p>
                                                    @endif
                                                </div>
                                                {{-- Matches --}}
                                                <div class="d-flex flex-column gap-4">
                                                    @foreach($bracket->matches->where('round', $round)->sortBy('match_order') as $match)
                                                        <div class="match-card rounded-4 border-0 shadow-sm overflow-hidden" style="background: white;">
                                                            {{-- Participant 1 --}}
                                                            @php
                                                                $p1Winner = $match->winner_id == $match->participant_1_id;
                                                                $p2Winner = $match->winner_id == $match->participant_2_id;
                                                            @endphp
                                                            <div class="d-flex align-items-center justify-content-between px-3 py-2 {{ $p1Winner ? 'bg-success bg-opacity-10' : '' }} border-bottom">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    @if($p1Winner)
                                                                        <span class="material-icons text-success small">check_circle</span>
                                                                    @endif
                                                                    <span class="small {{ $p1Winner ? 'fw-bold text-success' : 'text-dark' }}">
                                                                        {{ $match->participant1?->display_name ?? 'TBD' }}
                                                                    </span>
                                                                </div>
                                                                <span class="badge {{ $p1Winner ? 'bg-success' : 'bg-light text-dark' }} rounded-pill px-2">
                                                                    {{ $match->score_1 ?? '-' }}
                                                                </span>
                                                            </div>
                                                            {{-- Participant 2 --}}
                                                            <div class="d-flex align-items-center justify-content-between px-3 py-2 {{ $p2Winner ? 'bg-success bg-opacity-10' : '' }}">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    @if($p2Winner)
                                                                        <span class="material-icons text-success small">check_circle</span>
                                                                    @endif
                                                                    <span class="small {{ $p2Winner ? 'fw-bold text-success' : 'text-dark' }}">
                                                                        {{ $match->participant2?->display_name ?? 'TBD' }}
                                                                    </span>
                                                                </div>
                                                                <span class="badge {{ $p2Winner ? 'bg-success' : 'bg-light text-dark' }} rounded-pill px-2">
                                                                    {{ $match->score_2 ?? '-' }}
                                                                </span>
                                                            </div>
                                                            {{-- Set Result Button --}}
                                                            @if(auth()->id() == $event->created_by && !$match->winner_id && $match->participant_1_id && $match->participant_2_id)
                                                                <div class="px-3 py-2 bg-light border-top">
                                                                    <button class="btn btn-sm btn-primary w-100 rounded-pill"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#matchResultModal{{ $match->id }}">
                                                                        <span class="material-icons small align-middle me-1">edit</span> Set Hasil
                                                                    </button>
                                                                </div>

                                                                {{-- Match Result Modal --}}
                                                                <div class="modal fade" id="matchResultModal{{ $match->id }}" tabindex="-1">
                                                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                                                        <div class="modal-content rounded-4 border-0 shadow">
                                                                            <form
                                                                                action="{{ route('spaces.events.brackets.matches.result', ['space' => $event->space->slug, 'event' => $event->uuid, 'bracket' => $bracket->uuid, 'match' => $match->id]) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <div class="modal-body p-4">
                                                                                    <h6 class="fw-bold mb-3 text-center">Hasil Pertandingan</h6>
                                                                                    <div class="mb-3">
                                                                                        <label class="form-label small fw-bold">Pemenang</label>
                                                                                        <select name="winner_id"
                                                                                            class="form-select rounded-3 bg-light border-0"
                                                                                            required>
                                                                                            <option value="">Pilih Pemenang</option>
                                                                                            <option value="{{ $match->participant_1_id }}">
                                                                                                {{ $match->participant1?->display_name }}
                                                                                            </option>
                                                                                            <option value="{{ $match->participant_2_id }}">
                                                                                                {{ $match->participant2?->display_name }}
                                                                                            </option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="row g-2">
                                                                                        <div class="col-6">
                                                                                            <label class="form-label small">Skor {{ $match->participant1?->display_name }}</label>
                                                                                            <input type="text" name="score_1"
                                                                                                class="form-control rounded-3 bg-light border-0 text-center"
                                                                                                placeholder="0">
                                                                                        </div>
                                                                                        <div class="col-6">
                                                                                            <label class="form-label small">Skor {{ $match->participant2?->display_name }}</label>
                                                                                            <input type="text" name="score_2"
                                                                                                class="form-control rounded-3 bg-light border-0 text-center"
                                                                                                placeholder="0">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-primary rounded-pill px-4 w-100">Simpan Hasil</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @endif

                        </div>
                    @empty
                        <p class="text-muted small text-center py-3 mb-0">Belum ada bracket lomba untuk acara ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Create Bracket Modal --}}
            @if(auth()->id() == $event->created_by)
                <div class="modal fade" id="createBracketModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Buat Bracket Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form
                                action="{{ route('spaces.events.brackets.store', ['space' => $event->space->slug, 'event' => $event->uuid]) }}"
                                method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nama Bracket</label>
                                        <input type="text" name="title" class="form-control rounded-3 bg-light border-0"
                                            placeholder="Misal: Tournament Mobile Legends" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Deskripsi (Opsional)</label>
                                        <textarea name="description" class="form-control rounded-3 bg-light border-0"
                                            rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Maksimal Peserta</label>
                                        <select name="max_participants" class="form-select rounded-3 bg-light border-0"
                                            required>
                                            <option value="4">4 Peserta</option>
                                            <option value="8">8 Peserta</option>
                                            <option value="16" selected>16 Peserta</option>
                                            <option value="32">32 Peserta</option>
                                            <option value="64">64 Peserta</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Buat Bracket</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 bg-light rounded-4 p-4 shadow-sm h-100">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="material-icons text-primary fs-5">how_to_reg</span>
                    Status Kehadiran
                </h6>
                <p class="text-muted small mb-4">Konfirmasi kehadiran Anda untuk acara ini.</p>

                <div class="d-flex flex-column gap-2 mb-4" id="rsvpActions">
                    @php
                        $userRsvp = $event->attendees()->where('user_id', auth()->id())->first();
                        $status = $userRsvp ? $userRsvp->status : null;
                    @endphp

                    <button onclick="updateRSVP('going')"
                        class="btn {{ $status === 'going' ? 'btn-primary border-primary' : 'btn-white border' }} rounded-4 text-start px-3 py-3 d-flex align-items-center justify-content-between shadow-xs transition-all">
                        <span class="d-flex align-items-center gap-3">
                            <span
                                class="material-icons {{ $status === 'going' ? 'text-white' : 'text-primary' }}">check_circle</span>
                            <span class="fw-bold {{ $status === 'going' ? 'text-white' : 'text-dark' }}">Akan
                                Hadir</span>
                        </span>
                        @if($status === 'going') <span class="material-icons text-white small">done_all</span>
                        @endif
                    </button>

                    <button onclick="updateRSVP('maybe')"
                        class="btn {{ $status === 'maybe' ? 'btn-info text-white' : 'btn-white border' }} rounded-4 text-start px-3 py-3 d-flex align-items-center justify-content-between shadow-xs transition-all">
                        <span class="d-flex align-items-center gap-3">
                            <span
                                class="material-icons {{ $status === 'maybe' ? 'text-white' : 'text-info' }}">help_outline</span>
                            <span class="fw-bold {{ $status === 'maybe' ? 'text-white' : 'text-dark' }}">Mungkin</span>
                        </span>
                        @if($status === 'maybe') <span class="material-icons text-white small">done_all</span>
                        @endif
                    </button>

                    <button onclick="updateRSVP('not_going')"
                        class="btn {{ $status === 'not_going' ? 'btn-danger' : 'btn-white border' }} rounded-4 text-start px-3 py-3 d-flex align-items-center justify-content-between shadow-xs transition-all">
                        <span class="d-flex align-items-center gap-3">
                            <span
                                class="material-icons {{ $status === 'not_going' ? 'text-white' : 'text-danger' }}">cancel</span>
                            <span class="fw-bold {{ $status === 'not_going' ? 'text-white' : 'text-dark' }}">Tidak
                                Hadir</span>
                        </span>
                        @if($status === 'not_going') <span class="material-icons text-white small">done_all</span>
                        @endif
                    </button>
                </div>

                <div class="border-top pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold small mb-0">Peserta ({{ $event->attendees->count() }})</h6>
                        @if(auth()->id() == $event->created_by)
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal"
                                data-bs-target="#attendanceListModal">
                                <span class="material-icons small align-middle">list</span> Lihat Semua
                            </button>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($event->attendees->take(8) as $attendee)
                            <div class="avatar-sm rounded-circle border border-2 border-white shadow-xs overflow-hidden"
                                title="{{ $attendee->user->name }} ({{ ucfirst($attendee->status) }})"
                                style="width: 32px; height: 32px;">
                                <img src="{{ $attendee->user->avatar_url }}" class="w-100 h-100 object-fit-cover">
                            </div>
                        @empty
                            <div class="py-2 px-3 rounded-pill bg-white border small text-muted italic">Mulai ramekan
                                acara
                                ini!</div>
                        @endforelse
                        @if($event->attendees->count() > 8)
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center x-small fw-bold shadow-xs"
                                style="width: 32px; height: 32px;">
                                +{{ $event->attendees->count() - 8 }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Attendance List Modal (Owner Only) --}}
                @if(auth()->id() == $event->created_by)
                    <div class="modal fade" id="attendanceListModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                                {{-- Header --}}
                                <div class="modal-header border-0 px-4 pt-4 pb-2">
                                    <div>
                                        <h5 class="modal-title fw-bold mb-1">Daftar Kehadiran</h5>
                                        <p class="text-muted small mb-0">{{ $event->title }}</p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                
                                {{-- Stats Cards --}}
                                <div class="px-4 pb-3">
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <div class="text-center p-3 rounded-3" style="background: #e8f5e9;">
                                                <div class="fw-bold fs-4 text-success">{{ $event->attendees->where('status', 'going')->count() }}</div>
                                                <div class="small text-success">Hadir</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center p-3 rounded-3" style="background: #e3f2fd;">
                                                <div class="fw-bold fs-4 text-info">{{ $event->attendees->where('status', 'maybe')->count() }}</div>
                                                <div class="small text-info">Mungkin</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center p-3 rounded-3" style="background: #ffebee;">
                                                <div class="fw-bold fs-4 text-danger">{{ $event->attendees->where('status', 'not_going')->count() }}</div>
                                                <div class="small text-danger">Tidak</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Attendee List --}}
                                <div class="modal-body px-4 pt-0" style="max-height: 400px;">
                                    @forelse($event->attendees as $attendee)
                                        <div class="d-flex align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            {{-- Avatar --}}
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" 
                                                style="width: 44px; height: 44px; min-width: 44px;">
                                                <img src="{{ $attendee->user->avatar_url }}" class="rounded-circle" style="width: 44px; height: 44px; object-fit: cover;">
                                            </div>
                                            {{-- Info --}}
                                            <div class="flex-grow-1 min-width-0">
                                                <div class="fw-semibold text-dark text-truncate">{{ $attendee->user->name }}</div>
                                                <small class="text-muted">@{{ $attendee->user->username }}</small>
                                            </div>
                                            {{-- Status Badge --}}
                                            @php
                                                $statusConfig = [
                                                    'going' => ['bg' => 'bg-success', 'text' => 'Hadir'],
                                                    'maybe' => ['bg' => 'bg-info', 'text' => 'Mungkin'],
                                                    'not_going' => ['bg' => 'bg-danger', 'text' => 'Tidak']
                                                ];
                                                $config = $statusConfig[$attendee->status] ?? $statusConfig['not_going'];
                                            @endphp
                                            <span class="badge {{ $config['bg'] }} rounded-pill px-3 py-2">{{ $config['text'] }}</span>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                                <span class="material-icons text-muted" style="font-size: 32px;">group_off</span>
                                            </div>
                                            <h6 class="fw-bold text-dark">Belum Ada Peserta</h6>
                                            <p class="text-muted small mb-0">Bagikan acara ini untuk mengundang peserta.</p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Footer --}}
                                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                                    <button type="button" class="btn btn-light rounded-pill px-4 w-100" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>