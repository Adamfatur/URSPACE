@auth
    <!-- Edit Thread Modal (Precision Clone of Create Modal) -->
    <div class="modal fade" id="editThreadModal" tabindex="-1" aria-hidden="true">
        <style>
            .hover-bg-light:hover {
                background-color: #f8f9fa;
            }

            .cursor-pointer {
                cursor: pointer;
            }

            .edit-tag-pill {
                cursor: pointer;
                transition: all 0.2s;
            }

            .edit-tag-pill.active {
                background-color: #0d6efd !important;
                color: white !important;
                border-color: #0d6efd !important;
            }

            .edit-media-preview-container {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            .edit-media-preview-remove {
                position: absolute;
                top: 10px;
                right: 10px;
                background: rgba(0, 0, 0, 0.5);
                color: white;
                border: none;
                border-radius: 50%;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s;
            }

            .edit-media-preview-remove:hover {
                background: rgba(0, 0, 0, 0.8);
            }
        </style>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <form id="editThreadForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="remove_image" id="editRemoveImage" value="0">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center fw-bold" id="editModalTitle">Edit Thread</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body pt-3">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                                    style="width: 45px; height: 45px; overflow:hidden;">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="User" class="w-100 h-100 object-fit-cover">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold fs-6">{{ auth()->user()->username }}</span>
                                    <div class="format-selector" id="editFormatSelector">
                                        <input type="radio" class="btn-check" name="thread_type" id="edit_type_thread"
                                            value="short_thread">
                                        <label class="btn btn-sm rounded-pill px-3" for="edit_type_thread">Thread</label>

                                        <input type="radio" class="btn-check" name="thread_type" id="edit_type_article"
                                            value="article">
                                        <label class="btn btn-sm rounded-pill px-3" for="edit_type_article">Artikel</label>
                                    </div>
                                </div>

                                <!-- Category Select -->
                                <div class="category-select-wrapper mb-3" id="editCategorySelectWrapper">
                                    <select name="category_id"
                                        class="form-select form-select-sm border-0 bg-light rounded-3"
                                        id="editCategorySelect">
                                        <option value="" disabled>Bahas di ruang mana?</option>
                                        @foreach(\App\Models\Category::all() as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Title Input -->
                                <div id="editTitleContainer" class="mb-3 d-none border-bottom pb-2">
                                    <input type="text" name="title" id="editThreadTitle"
                                        class="form-control border-0 p-0 fw-bold fs-4 shadow-none text-dark"
                                        placeholder="Judul Thread..." style="background: transparent;">
                                </div>

                                <textarea name="content" id="editThreadContent"
                                    class="form-control border-0 p-0 shadow-none mb-3" rows="2"
                                    placeholder="Apa yang sedang kamu pikirkan?" style="resize: none;"></textarea>

                                <!-- Tag Selection -->
                                <div id="editTagSection" class="mb-3 d-none">
                                    <div class="d-flex flex-wrap gap-2 mb-2" id="editTagPillsContainer">
                                        @foreach(\App\Models\Tag::take(10)->get() as $tag)
                                            <span class="badge rounded-pill bg-light text-muted border edit-tag-pill px-2 py-1"
                                                data-tag-id="{{ $tag->id }}">#{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                    <div id="editSelectedTagsContainer"></div>
                                </div>

                                <!-- Poll Section -->
                                <div id="editPollSection"
                                    class="mb-3 d-none p-3 rounded-4 bg-light border border-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-muted d-flex align-items-center gap-1">
                                            <span class="material-icons" style="font-size: 16px;">poll</span> Polling
                                        </span>
                                        <button type="button"
                                            class="btn btn-sm btn-link text-danger text-decoration-none p-0"
                                            id="editRemovePollBtn">Hapus</button>
                                    </div>
                                    <div id="editPollOptionsContainer">
                                        <!-- Populated via JS -->
                                    </div>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary rounded-pill w-100 mt-1 fw-bold"
                                        id="editAddOptionBtn">+ Tambah Opsi</button>
                                </div>

                                <!-- Video/Link Section -->
                                <div id="editVideoSection"
                                    class="mb-3 d-none p-3 rounded-4 bg-light border border-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-muted d-flex align-items-center gap-1">
                                            <span class="material-icons" style="font-size: 16px;">link</span> Media URL
                                        </span>
                                        <button type="button"
                                            class="btn btn-sm btn-link text-danger text-decoration-none p-0"
                                            id="editRemoveVideoBtn">Hapus</button>
                                    </div>
                                    <input type="url" name="video_url" id="editVideoUrl"
                                        class="form-control form-control-sm rounded-3 border-0 bg-white"
                                        placeholder="Tempel tautan eksternal di sini (YouTube, PDF, Slide, dll)...">

                                    <!-- Video Preview Area -->
                                    <div id="editUrlPreviewArea" class="mt-2 d-none">
                                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden border">
                                            <iframe id="editYoutubePreview" src="" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2" id="editMediaPreview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center py-3 px-4">
                        <div class="d-flex gap-3 align-items-center">
                            <label class="cursor-pointer mb-0 p-2 rounded-circle hover-bg-light transition-all"
                                title="Unggah Media">
                                <span class="material-icons text-muted fs-5">attach_file</span>
                                <input type="file" name="image" class="d-none" id="editFileInput"
                                    accept="image/*,video/*,application/pdf">
                            </label>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="editPollBtn"
                                title="Buat Polling">
                                <span class="material-icons text-muted fs-5">poll</span>
                            </div>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="editVideoBtn"
                                title="Sematkan Tautan Eksternal">
                                <span class="material-icons text-muted fs-5">link</span>
                            </div>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="editTagBtn"
                                title="Tambah Tag">
                                <span class="material-icons text-muted fs-5">sell</span>
                            </div>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="editCodeBtn"
                                title="Sematkan Kode">
                                <span class="material-icons text-muted fs-5">code</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <small id="editCharCounter" class="text-muted fw-bold small">256</small>
                            <button type="submit" class="btn btn-posting text-white shadow-sm px-4 rounded-pill fw-bold"
                                id="editSubmitBtn">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endauth