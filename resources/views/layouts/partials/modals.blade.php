@auth
    @include('layouts.partials.create_thread_modal')
@endauth

{{-- Comment Modal --}}
<div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Balas ke @<span id="commentTargetUser"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="commentForm">
                <div class="modal-body">
                    <textarea class="form-control border-0 bg-light rounded-4 p-3" id="commentContent" rows="4"
                        placeholder="Tulis balasanmu..." maxlength="256" required></textarea>
                    <div class="text-end mt-2">
                        <small class="text-muted"><span id="commentCharCount">0</span>/256</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Report Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Laporkan Konten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reportForm">
                <div class="modal-body">
                    <p class="text-muted small mb-3">Beri tahu kami mengapa konten ini melanggar pedoman komunitas.</p>
                    <select class="form-select border-0 bg-light rounded-4 p-3 mb-3" id="reportReason" required>
                        <option value="">Pilih alasan...</option>
                        <option value="Spam">Spam</option>
                        <option value="Pelecehan">Pelecehan / Perundungan</option>
                        <option value="Ujaran Kebencian">Ujaran Kebencian</option>
                        <option value="Konten Tidak Pantas">Konten Tidak Pantas</option>
                        <option value="Informasi Palsu">Informasi Palsu (Hoax)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <textarea class="form-control border-0 bg-light rounded-4 p-3" id="reportExtra" rows="2"
                        placeholder="Detail tambahan (opsional)"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Laporkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@auth
    @include('layouts.partials.edit_thread_modal')
@endauth

{{-- Confirm Delete Modal --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <span class="material-icons" style="font-size: 48px;">delete_outline</span>
                </div>
                <h5 class="fw-bold">Hapus Thread?</h5>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan. Konfirmasi hapus?</p>
                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-danger rounded-pill fw-bold" id="confirmDeleteBtn">Ya,
                        Hapus</button>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Comment Success Modal --}}
<div class="modal fade" id="commentSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3">
                    <span class="material-icons" style="font-size: 48px;">check_circle_outline</span>
                </div>
                <h5 class="fw-bold">Balasan Terkirim!</h5>
                <p class="text-muted small">Ingin melihat thread lengkap beserta semua balasannya?</p>
                <div class="d-grid gap-2 mt-4">
                    <a href="#" class="btn btn-primary rounded-pill fw-bold" id="viewFullThreadBtn">Lihat Thread</a>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Share Modal --}}
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-5 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">Bagikan</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="share-grid d-grid gap-3">
                    {{-- Copy Link --}}
                    <button class="share-card btn border-0 p-3 rounded-4 d-flex align-items-center gap-3 transition-all"
                        onclick="Forum.copyLink(document.getElementById('shareUrlInput').value)">
                        <div
                            class="share-icon-wrapper bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                            <span class="material-icons text-primary">content_copy</span>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold text-dark small">Salin Tautan</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Copy link ke clipboard</div>
                        </div>
                    </button>

                    {{-- WhatsApp --}}
                    <a href="#" id="shareWA" target="_blank"
                        class="share-card btn border-0 p-3 rounded-4 d-flex align-items-center gap-3 transition-all text-decoration-none">
                        <div
                            class="share-icon-wrapper bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#25D366"
                                viewBox="0 0 16 16">
                                <path
                                    d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
                            </svg>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold text-dark small">WhatsApp</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Bagikan ke WhatsApp</div>
                        </div>
                    </a>

                    {{-- X (Twitter) --}}
                    <a href="#" id="shareX" target="_blank"
                        class="share-card btn border-0 p-3 rounded-4 d-flex align-items-center gap-3 transition-all text-decoration-none">
                        <div
                            class="share-icon-wrapper bg-dark-subtle rounded-circle d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z" />
                            </svg>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold text-dark small">X (Twitter)</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Bagikan ke X</div>
                        </div>
                    </a>

                    {{-- Facebook --}}
                    <a href="#" id="shareFB" target="_blank"
                        class="share-card btn border-0 p-3 rounded-4 d-flex align-items-center gap-3 transition-all text-decoration-none">
                        <div class="share-icon-wrapper bg-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="--bs-bg-opacity: .15;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#0866FF"
                                viewBox="0 0 16 16">
                                <path
                                    d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                            </svg>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold text-dark small">Facebook</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Bagikan ke Facebook</div>
                        </div>
                    </a>
                </div>
                <input type="hidden" id="shareUrlInput">
            </div>
        </div>
    </div>
</div>

{{-- Image Viewer Modal --}}
<div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(5px);">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 95vw;">
        <div
            class="modal-content rounded-4 border-0 bg-transparent shadow-none h-100 d-flex align-items-center justify-content-center pointer-event-none">
            <div class="modal-header border-0 pb-0 position-absolute w-100 z-3 p-4"
                style="top: 0; pointer-events: none;">
                <div class="ms-auto" style="pointer-events: auto;">
                    <button type="button" class="btn-close btn-close-white p-3 rounded-circle shadow-lg"
                        data-bs-dismiss="modal"
                        style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(8px); opacity: 1;"
                        aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 text-center position-relative w-100 h-100 d-flex align-items-center justify-content-center"
                style="pointer-events: auto;">
                <img id="imageViewerImg" src="" class="img-fluid rounded-4 shadow-lg"
                    style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
                @auth
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-5 z-3">
                        <a id="imageViewerDownload" href="" download
                            class="btn btn-dark bg-opacity-75 rounded-pill px-4 py-2 border border-secondary fw-bold d-flex align-items-center gap-2 shadow-lg"
                            style="backdrop-filter: blur(8px);">
                            <span class="material-icons">download</span> Unduh Gambar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && in_array(auth()->user()->role, ['admin', 'global_admin', 'univ_admin']))
    @include('layouts.partials.shadow_ban_modal')
@endif

{{-- Logout Confirmation Modal --}}
<div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <span class="material-icons" style="font-size: 48px;">logout</span>
                </div>
                <h5 class="fw-bold">Yakin ingin keluar?</h5>
                <p class="text-muted small">Anda harus bergabung kembali jika ingin mengakses fitur forum.</p>
                <div class="d-grid gap-2 mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100">Ya, Keluar</button>
                    </form>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>