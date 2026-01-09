<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <!-- Search Header -->
            <div class="modal-header border-0 p-4 pb-0">
                <div class="position-relative w-100">
                    <span
                        class="material-icons position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-3">search</span>
                    <input type="text" id="searchInput"
                        class="form-control form-control-lg border-0 bg-light rounded-pill ps-5 py-3 shadow-none fw-medium"
                        placeholder="Cari forum, pengguna, atau diskusi..." style="font-size: 1.1rem;"
                        autocomplete="off">

                    <button type="button" id="clearSearchBtn"
                        class="btn btn-link link-secondary text-decoration-none position-absolute top-50 end-0 translate-middle-y me-3 d-none p-0">
                        <span class="material-icons">close</span>
                    </button>

                    <div class="spinner-border spinner-border-sm text-primary position-absolute top-50 end-0 translate-middle-y me-3 d-none"
                        id="searchSpinner" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- Search Body -->
            <div class="modal-body p-4" style="min-height: 400px;">

                <!-- Initial State -->
                <div id="searchInitialState" class="text-center py-5 text-muted opacity-50">
                    <span class="material-icons display-1 mb-3">manage_search</span>
                    <p class="fs-5">Ketik untuk mulai mencari</p>
                </div>

                <!-- Empty State -->
                <div id="searchEmptyState" class="text-center py-5 d-none">
                    <span class="material-icons display-1 mb-3 text-muted opacity-25">search_off</span>
                    <h5 class="fw-bold text-muted">Tidak ditemukan</h5>
                    <p class="text-muted small">Kami tidak dapat menemukan hasil untuk pencarian Anda.</p>
                </div>

                <!-- Results Container -->
                <div id="searchResults" class="d-none">
                    <!-- Users Section -->
                    <div id="usersSection" class="mb-4 d-none">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3 ls-1">Pengguna</h6>
                        <div class="list-group list-group-flush" id="usersList">
                            <!-- JS driven content -->
                        </div>
                    </div>

                    <!-- Spaces Section -->
                    <div id="spacesSection" class="mb-4 d-none">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3 ls-1">Ruang Diskusi (URSpace)</h6>
                        <div class="row g-2" id="spacesList">
                            <!-- JS driven content -->
                        </div>
                    </div>

                    <!-- Threads Section -->
                    <div id="threadsSection" class="d-none">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3 ls-1">Diskusi</h6>
                        <div class="d-flex flex-column gap-2" id="threadsList">
                            <!-- JS driven content -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light py-2 justify-content-center text-muted small">
                <span class="d-flex align-items-center gap-2">
                    <span class="badge bg-white border text-muted px-2">ESC</span> untuk menutup
                </span>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for Modal */
    #searchModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    #searchModal .modal-body::-webkit-scrollbar-track {
        background: transparent;
    }

    #searchModal .modal-body::-webkit-scrollbar-thumb {
        background-color: #e0e0e0;
        border-radius: 20px;
    }

    /* Hover effect for results */
    .search-result-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .ls-1 {
        letter-spacing: 1px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchModal = document.getElementById('searchModal');
        const searchInput = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearchBtn');
        const spinner = document.getElementById('searchSpinner');

        const initialState = document.getElementById('searchInitialState');
        const emptyState = document.getElementById('searchEmptyState');
        const searchResults = document.getElementById('searchResults');

        const usersSection = document.getElementById('usersSection');
        const usersList = document.getElementById('usersList');
        const spacesSection = document.getElementById('spacesSection');
        const spacesList = document.getElementById('spacesList');
        const threadsSection = document.getElementById('threadsSection');
        const threadsList = document.getElementById('threadsList');

        let debounceTimer;

        // Auto focus input on modal open
        searchModal.addEventListener('shown.bs.modal', () => {
            searchInput.focus();
        });

        // Clear button
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.focus();
            resetView();
            toggleClearBtn();
        });

        // Input Handler
        searchInput.addEventListener('input', (e) => {
            toggleClearBtn();
            const query = e.target.value.trim();

            clearTimeout(debounceTimer);

            if (query.length < 2) {
                resetView();
                return;
            }

            // Show loading
            spinner.classList.remove('d-none');
            clearBtn.classList.add('d-none'); // Hide X while loading

            debounceTimer = setTimeout(() => {
                fetchSearchResults(query);
            }, 400);
        });

        function toggleClearBtn() {
            if (searchInput.value.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
        }

        function resetView() {
            searchResults.classList.add('d-none');
            emptyState.classList.add('d-none');
            initialState.classList.remove('d-none');
            spinner.classList.add('d-none');
        }

        async function fetchSearchResults(query) {
            try {
                const response = await fetch(`/search/query?query=${encodeURIComponent(query)}`);
                const data = await response.json();

                spinner.classList.add('d-none');
                clearBtn.classList.remove('d-none');

                initialState.classList.add('d-none');

                if (data.users.length === 0 && data.spaces.length === 0 && data.threads.length === 0) {
                    emptyState.classList.remove('d-none');
                    searchResults.classList.add('d-none');
                } else {
                    emptyState.classList.add('d-none');
                    searchResults.classList.remove('d-none');
                    renderResults(data);
                }

            } catch (error) {
                console.error('Search error:', error);
                spinner.classList.add('d-none');
            }
        }

        function renderResults(data) {
            // Users
            if (data.users.length > 0) {
                usersSection.classList.remove('d-none');
                usersList.innerHTML = data.users.map(user => `
                    <a href="/profile/${user.username}" class="list-group-item list-group-item-action border-0 rounded-3 p-2 d-flex align-items-center gap-3 mb-1 search-result-item">
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; overflow:hidden;">
                            ${user.avatar ? `<img src="${user.avatar}" class="w-100 h-100 object-fit-cover">` : '<span class="material-icons text-white small">person</span>'}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${user.name}</div>
                            <small class="text-muted">@${user.username}</small>
                        </div>
                    </a>
                `).join('');
            } else {
                usersSection.classList.add('d-none');
            }

            // Spaces
            if (data.spaces.length > 0) {
                spacesSection.classList.remove('d-none');
                spacesList.innerHTML = data.spaces.map(space => `
                    <div class="col-md-6">
                        <a href="/spaces/${space.slug}" class="d-flex align-items-center p-2 border rounded-3 text-decoration-none bg-white hover-shadow transition-all text-dark">
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 48px; height: 48px; overflow:hidden;">
                                ${space.cover_image ? `<img src="/storage/${space.cover_image}" class="w-100 h-100 object-fit-cover">` : '<span class="material-icons text-muted">dns</span>'}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-truncate">${space.name}</div>
                                <div class="small text-muted d-flex align-items-center gap-1">
                                    <span class="material-icons" style="font-size: 12px;">group</span> ${space.members_count} Anggota
                                </div>
                            </div>
                        </a>
                    </div>
                `).join('');
            } else {
                spacesSection.classList.add('d-none');
            }

            // Threads
            if (data.threads.length > 0) {
                threadsSection.classList.remove('d-none');
                threadsList.innerHTML = data.threads.map(thread => `
                    <a href="/threads/${thread.uuid}" class="d-block p-3 border rounded-3 text-decoration-none bg-white hover-shadow transition-all search-result-item">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            ${thread.space ? `<span class="badge bg-light text-dark border-0 px-2 rounded-pill small" style="background-color: #f1f5f1 !important; color: #4a6f4a !important;"><span class="material-icons align-top" style="font-size: 12px; margin-top:2px;">dns</span> ${thread.space.name}</span>` : ''}
                            <div class="d-flex align-items-center gap-1 small text-muted">
                                ${thread.user.avatar ? `<img src="/storage/${thread.user.avatar}" class="rounded-circle" style="width: 16px; height: 16px;">` : '<span class="material-icons" style="font-size: 16px;">account_circle</span>'}
                                <span>${thread.user.username}</span>
                                <span>•</span>
                                <span>${thread.created_at}</span>
                            </div>
                        </div>
                        <h6 class="fw-bold text-dark mb-1 text-truncate d-flex align-items-center gap-2">
                            ${thread.has_video ? '<span class="material-icons text-danger fs-5">play_circle</span>' : ''}
                            ${thread.type === 'article' ? '<span class="material-icons text-primary fs-5">menu_book</span>' : ''}
                            ${thread.title || 'Postingan...'}
                        </h6>
                        <p class="text-muted small mb-0 text-truncate">${thread.content || '...'}</p>
                    </a>
                `).join('');
            } else {
                threadsSection.classList.add('d-none');
            }
        }
    });
</script>