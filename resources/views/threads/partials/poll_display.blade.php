@php
    $totalVotes = $thread->pollOptions->sum('votes_count');
    $userVoted = auth()->check() ? $thread->userVoted(auth()->id()) : false;
@endphp

<div id="poll-container-{{ $thread->uuid }}" class="mb-3 p-4 rounded-4 border bg-white shadow-sm">
    <div class="fw-bold text-uppercase small text-muted mb-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <span class="material-icons text-primary" style="font-size: 20px;">poll</span>
            Polling
        </div>
        @if($totalVotes > 0)
            <span class="badge bg-light text-muted border fw-normal">{{ $totalVotes }} Suara</span>
        @endif
    </div>

    <div class="d-grid gap-3">
        @foreach($thread->pollOptions as $option)
            @php
                $percentage = $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100) : 0;
            @endphp
            <div class="mb-0">
                @if(!$userVoted)
                    <button type="button"
                        onclick="console.log('Poll option clicked: {{ $option->id }}'); @auth Forum.submitVote('{{ $thread->uuid }}', {{ $option->id }}) @else Forum.guestAction() @endauth"
                        class="btn w-100 text-start rounded-4 bg-white border p-3 shadow-sm hover-bg-light position-relative">
                        <span class="fw-bold text-dark">{{ $option->option_text }}</span>
                    </button>
                @else
                    <div class="w-100 p-3 rounded-4 border bg-light shadow-sm position-relative overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center mb-1 position-relative"
                            style="z-index: 2;">
                            <span class="fw-bold text-dark">{{ $option->option_text }}</span>
                            <span class="fw-bold text-primary small">{{ $percentage }}%</span>
                        </div>
                        <div class="progress rounded-pill bg-white border shadow-xs position-relative"
                            style="height: 10px; z-index: 2;">
                            <div class="progress-bar bg-primary shadow-sm" role="progressbar" style="width: {{ $percentage }}%"
                                aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if(!$userVoted)
        <div class="mt-3 small text-muted d-flex align-items-center gap-2 opacity-75">
            <span class="material-icons" style="font-size: 16px;">info_outline</span>
            Pilih salah satu opsi untuk melihat hasil.
        </div>
    @endif
</div>