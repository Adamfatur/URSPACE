<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-black fs-4" id="createEventModalLabel">Buat Acara Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('spaces.events.store', $space->slug) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <!-- Left Column: Image & Basic Info -->
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Cover Acara</label>
                                <div class="ratio ratio-4x3 bg-light rounded-4 overflow-hidden position-relative border border-dashed"
                                    onclick="document.getElementById('eventCoverInput').click()"
                                    style="cursor: pointer; border-style: dashed !important; border-width: 2px !important;">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted"
                                        id="coverPreviewPlaceholder">
                                        <span class="material-icons display-4 mb-2">add_photo_alternate</span>
                                        <span class="small fw-bold">Upload Cover</span>
                                    </div>
                                    <img id="coverPreview" src="" class="w-100 h-100 object-fit-cover d-none"
                                        alt="Preview">
                                </div>
                                <input type="file" name="cover_image" id="eventCoverInput" class="d-none"
                                    accept="image/*" onchange="previewEventCover(this)">
                                <div class="form-text small text-center mt-2">Format: JPG, PNG (Max 2MB)</div>
                            </div>
                        </div>

                        <!-- Right Column: Details -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Acara</label>
                                <input type="text" name="title"
                                    class="form-control form-control-lg rounded-3 bg-light border-0"
                                    placeholder="Misal: Diskusi Mingguan" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea name="description" class="form-control rounded-3 bg-light border-0" rows="3"
                                    placeholder="Ceritakan detail acara..."></textarea>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted">Mulai</label>
                                    <input type="datetime-local" name="starts_at"
                                        class="form-control rounded-3 bg-light border-0" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted">Selesai (Opsional)</label>
                                    <input type="datetime-local" name="ends_at"
                                        class="form-control rounded-3 bg-light border-0">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold mb-2">Siapa yang bisa melihat?</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check card-radio">
                                        <input class="form-check-input" type="radio" name="visibility" id="visAll"
                                            value="all_members" checked>
                                        <label class="form-check-label" for="visAll">
                                            Member
                                        </label>
                                    </div>
                                    <div class="form-check card-radio">
                                        <input class="form-check-input" type="radio" name="visibility" id="visOpen"
                                            value="open">
                                        <label class="form-check-label" for="visOpen">
                                            Publik
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mt-3 pt-3 border-top">
                                <label class="form-label fw-bold">Lokasi Acara</label>
                                <div class="d-flex gap-2 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type"
                                            id="locOffline" value="offline" checked>
                                        <label class="form-check-label small" for="locOffline">
                                            <span class="material-icons align-middle small">place</span> Offline
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type" id="locOnline"
                                            value="online">
                                        <label class="form-check-label small" for="locOnline">
                                            <span class="material-icons align-middle small">videocam</span> Online
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="location_type" id="locHybrid"
                                            value="hybrid">
                                        <label class="form-check-label small" for="locHybrid">
                                            <span class="material-icons align-middle small">sync_alt</span> Hybrid
                                        </label>
                                    </div>
                                </div>
                                <input type="text" name="location_detail"
                                    class="form-control rounded-3 bg-light border-0"
                                    placeholder="Alamat / Link Meeting (Zoom, Google Meet, Discord, dll)">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Buat
                            Acara</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewEventCover(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('coverPreview').src = e.target.result;
                document.getElementById('coverPreview').classList.remove('d-none');
                document.getElementById('coverPreviewPlaceholder').classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>