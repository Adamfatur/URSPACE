<!-- Create Space Modal -->
<div class="modal fade" id="createSpaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-black text-sage-900 w-100 text-center">Bangun URSpace Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('spaces.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pt-4">
                    <!-- Icon Placeholder -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-sage-50 rounded-circle text-sage-600 mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="material-icons" style="font-size: 40px;">add_business</span>
                        </div>
                        <p class="text-muted small mb-0 px-4">Wadah baru untuk diskusi, berbagi ide, dan terhubung
                            dengan komunitasmu.</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-sage-900 small">Nama URSpace</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0 text-muted ps-3 rounded-start-4">
                                <span class="material-icons fs-5">tag</span>
                            </span>
                            <input type="text" name="name"
                                class="form-control bg-light border-0 shadow-none fs-6 rounded-end-4"
                                placeholder="Contoh: Pecinta Kucing, Alumni 2024" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-sage-900 small">Deskripsi Singkat</label>
                        <textarea name="description" rows="3"
                            class="form-control bg-light border-0 rounded-4 p-3 shadow-none"
                            placeholder="Ceritakan sedikit tentang apa yang akan dibahas di sini..." required
                            style="resize: none;"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-sage-900 small">Cover Image (Opsional)</label>
                        <div class="input-group">
                            <input type="file" name="cover_image"
                                class="form-control bg-light border-0 rounded-4 shadow-none" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm flex-grow-1">
                        Buat URSpace
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>