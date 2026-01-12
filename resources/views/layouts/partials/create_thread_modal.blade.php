@auth
    <!-- Threads-style Modal -->
    <div class="modal fade" id="createThreadModal" tabindex="-1" aria-hidden="true">
        <style>
            .space-mode #modalUserAvatar,
            .space-mode #modalUserName {
                display: none !important;
            }

            .hover-bg-light:hover {
                background-color: #f8f9fa;
            }

            .cursor-pointer {
                cursor: pointer;
            }

            .tag-pill {
                cursor: pointer;
                transition: all 0.2s;
            }

            .tag-pill.active {
                background-color: #0d6efd !important;
                color: white !important;
                border-color: #0d6efd !important;
            }

            .media-preview-container {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            .media-preview-remove {
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

            .media-preview-remove:hover {
                background: rgba(0, 0, 0, 0.8);
            }
        </style>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data"
                    id="createThreadForm">
                    @csrf
                    <!-- Space ID Hidden Input -->
                    <input type="hidden" name="space_id" id="spaceIdInput">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title w-100 text-center fw-bold" id="modalTitle">Thread Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body pt-3">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0" id="modalUserAvatar">
                                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center"
                                    style="width: 45px; height: 45px; overflow:hidden;">
                                <img src="{{ auth()->user()->avatar_url }}" alt="User" class="w-100 h-100 object-fit-cover">
                            </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold fs-6" id="modalUserName">{{ auth()->user()->username }}</span>
                                    <div class="format-selector" id="formatSelector">
                                        <input type="radio" class="btn-check" name="thread_type" id="type_thread"
                                            value="short_thread" checked>
                                        <label class="btn btn-sm rounded-pill px-3" for="type_thread">Thread</label>

                                        <input type="radio" class="btn-check" name="thread_type" id="type_article"
                                            value="article">
                                        <label class="btn btn-sm rounded-pill px-3" for="type_article">Artikel</label>
                                    </div>
                                </div>

                                <!-- Space/Category Select -->
                                <div class="category-select-wrapper mb-3" id="categorySelectWrapper">
                                    <select name="category_id"
                                        class="form-select form-select-sm border-0 bg-light rounded-3" id="categorySelect">
                                        <option value="" disabled selected>Bahas di ruang mana?</option>
                                        @foreach(\App\Models\Category::all() as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Title Input -->
                                <div id="threadTitleContainer" class="mb-3 d-none border-bottom pb-2">
                                    <input type="text" name="title"
                                        class="form-control border-0 p-0 fw-bold fs-4 shadow-none text-dark"
                                        placeholder="Judul Thread..." style="background: transparent;">
                                </div>

                                <textarea name="content" id="threadContent"
                                    class="form-control border-0 p-0 shadow-none mb-3" rows="2"
                                    placeholder="Apa yang sedang kamu pikirkan?" style="resize: none;"></textarea>

                                <!-- Tag Selection -->
                                <div id="tagSection" class="mb-3 d-none">
                                    <div class="d-flex flex-wrap gap-2 mb-2" id="tagPillsContainer">
                                        @foreach(\App\Models\Tag::take(10)->get() as $tag)
                                            <span class="badge rounded-pill bg-light text-muted border tag-pill px-2 py-1"
                                                data-tag-id="{{ $tag->id }}">#{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                    <div id="selectedTagsContainer"></div>
                                </div>

                                <!-- Poll Section -->
                                <div id="pollSection"
                                    class="mb-3 d-none p-3 rounded-4 bg-light border border-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-muted d-flex align-items-center gap-1">
                                            <span class="material-icons" style="font-size: 16px;">poll</span> Polling
                                        </span>
                                        <button type="button"
                                            class="btn btn-sm btn-link text-danger text-decoration-none p-0"
                                            id="removePollBtn">Hapus</button>
                                    </div>
                                    <div id="pollOptionsContainer">
                                        <input type="text" name="poll_options[]"
                                            class="form-control form-control-sm mb-2 rounded-3 border-0 bg-white"
                                            placeholder="Opsi 1">
                                        <input type="text" name="poll_options[]"
                                            class="form-control form-control-sm mb-2 rounded-3 border-0 bg-white"
                                            placeholder="Opsi 2">
                                    </div>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary rounded-pill w-100 mt-1 fw-bold"
                                        id="addOptionBtn">+ Tambah Opsi</button>
                                </div>

                                <!-- Video/Link Section -->
                                <div id="videoSection"
                                    class="mb-3 d-none p-3 rounded-4 bg-light border border-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-muted d-flex align-items-center gap-1">
                                            <span class="material-icons" style="font-size: 16px;">link</span> Media URL
                                        </span>
                                        <button type="button"
                                            class="btn btn-sm btn-link text-danger text-decoration-none p-0"
                                            id="removeVideoBtn">Hapus</button>
                                    </div>
                                    <input type="url" name="video_url" id="videoUrlInput"
                                        class="form-control form-control-sm rounded-3 border-0 bg-white"
                                        placeholder="Tempel tautan eksternal di sini (YouTube, PDF, Slide, dll)...">

                                    <!-- Video Preview Area -->
                                    <div id="urlPreviewArea" class="mt-2 d-none">
                                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden border">
                                            <iframe id="youtubePreview" src="" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2" id="mediaPreview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center py-3 px-4">
                        <div class="d-flex gap-3 align-items-center">
                            <label class="cursor-pointer mb-0 p-2 rounded-circle hover-bg-light transition-all"
                                title="Unggah Media">
                                <span class="material-icons text-muted fs-5">attach_file</span>
                                <input type="file" name="image" class="d-none" id="fileInput"
                                    accept="image/*,video/*,application/pdf">
                            </label>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="pollBtn"
                                title="Buat Polling">
                                <span class="material-icons text-muted fs-5">poll</span>
                            </div>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="videoBtn"
                                title="Sematkan Tautan Eksternal">
                                <span class="material-icons text-muted fs-5">link</span>
                            </div>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="tagBtn"
                                title="Tambah Tag">
                                <span class="material-icons text-muted fs-5">sell</span>
                            </div>
                            <div class="p-2 rounded-circle hover-bg-light cursor-pointer transition-all" id="codeBtn"
                                title="Sematkan Kode">
                                <span class="material-icons text-muted fs-5">code</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <small id="charCount" class="text-muted fw-bold small">256</small>
                            <button type="submit" class="btn btn-posting text-white shadow-sm px-4 rounded-pill fw-bold"
                                id="submitBtn">Posting</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createThreadModal = document.getElementById('createThreadModal');
            const threadContent = document.getElementById('threadContent');
            const charCount = document.getElementById('charCount');
            const typeThread = document.getElementById('type_thread');
            const typeArticle = document.getElementById('type_article');
            const threadTitleContainer = document.getElementById('threadTitleContainer');
            const submitBtn = document.getElementById('submitBtn');
            const fileInput = document.getElementById('fileInput');
            const mediaPreview = document.getElementById('mediaPreview');
            const spaceIdInput = document.getElementById('spaceIdInput');
            const categorySelectWrapper = document.getElementById('categorySelectWrapper');
            const categorySelect = document.getElementById('categorySelect');
            const formatSelector = document.getElementById('formatSelector');

            const pollBtn = document.getElementById('pollBtn');
            const videoBtn = document.getElementById('videoBtn');
            const tagBtn = document.getElementById('tagBtn');
            const codeBtn = document.getElementById('codeBtn');
            const pollSection = document.getElementById('pollSection');
            const videoSection = document.getElementById('videoSection');
            const tagSection = document.getElementById('tagSection');
            const videoUrlInput = document.getElementById('videoUrlInput');
            const urlPreviewArea = document.getElementById('urlPreviewArea');
            const youtubePreview = document.getElementById('youtubePreview');

            function updateCounter() {
                if (typeThread && typeThread.checked) {
                    const remaining = 256 - threadContent.value.length;
                    if (charCount) {
                        charCount.textContent = remaining;
                        charCount.classList.toggle('text-danger', remaining < 0);
                        charCount.classList.remove('d-none');
                    }
                    if (submitBtn) submitBtn.disabled = (remaining < 0);
                } else {
                    if (charCount) charCount.classList.add('d-none');
                    if (submitBtn) submitBtn.disabled = false;
                }

                // Auto-resize
                threadContent.style.height = 'auto';
                threadContent.style.height = threadContent.scrollHeight + 'px';
            }

            if (threadContent) {
                threadContent.addEventListener('input', updateCounter);
            }

            // Media Preview Handling
            if (fileInput && mediaPreview) {
                fileInput.addEventListener('change', function () {
                    mediaPreview.innerHTML = '';
                    const file = this.files[0];
                    if (file) {
                        const container = document.createElement('div');
                        container.className = 'media-preview-container mb-3';

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'media-preview-remove';
                        removeBtn.innerHTML = '<span class="material-icons fs-6">close</span>';
                        removeBtn.onclick = () => {
                            fileInput.value = '';
                            mediaPreview.innerHTML = '';
                        };

                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.className = 'w-100 rounded-4 border object-fit-cover shadow-sm';
                            img.style.maxHeight = '300px';
                            container.appendChild(img);
                        } else if (file.type.startsWith('video/')) {
                            const vid = document.createElement('video');
                            vid.src = URL.createObjectURL(file);
                            vid.className = 'w-100 rounded-4 border shadow-sm';
                            vid.style.maxHeight = '300px';
                            vid.controls = true;
                            container.appendChild(vid);
                        } else if (file.type === 'application/pdf') {
                            const div = document.createElement('div');
                            div.className = 'd-flex align-items-center gap-3 p-3 rounded-4 border bg-white shadow-sm';
                            div.innerHTML = `
                                            <span class="material-icons text-danger fs-1">picture_as_pdf</span>
                                            <div class="d-flex flex-column overflow-hidden">
                                                <span class="fw-bold text-truncate">${file.name}</span>
                                                <span class="small text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB • PDF Document</span>
                                            </div>
                                        `;
                            container.appendChild(div);
                        }

                        container.appendChild(removeBtn);
                        mediaPreview.appendChild(container);

                        // Clear video URL if file selected
                        videoUrlInput.value = '';
                        urlPreviewArea.classList.add('d-none');
                    }
                });
            }

            // Video Preview Logic
            if (videoUrlInput) {
                videoUrlInput.addEventListener('input', (e) => {
                    const url = e.target.value;
                    const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
                    const match = url.match(youtubeRegex);

                    if (match && match[1]) {
                        youtubePreview.src = `https://www.youtube.com/embed/${match[1]}`;
                        urlPreviewArea.classList.remove('d-none');
                        // Clear file if URL is set
                        fileInput.value = '';
                        mediaPreview.innerHTML = '';
                    } else {
                        urlPreviewArea.classList.add('d-none');
                        youtubePreview.src = '';
                    }
                });
            }

            // Tag Logic
            const tagPills = document.querySelectorAll('.tag-pill');
            const selectedTagsContainer = document.getElementById('selectedTagsContainer');

            tagPills.forEach(pill => {
                pill.addEventListener('click', () => {
                    const tagId = pill.getAttribute('data-tag-id');
                    const tagName = pill.textContent;

                    pill.classList.toggle('active');

                    const existingInput = selectedTagsContainer.querySelector(`input[value="${tagId}"]`);
                    if (existingInput) {
                        existingInput.remove();
                    } else {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'tags[]';
                        input.value = tagId;
                        selectedTagsContainer.appendChild(input);
                    }
                });
            });

            if (createThreadModal) {
                createThreadModal.addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;
                    const spaceId = button ? button.getAttribute('data-space-id') : null;

                    if (spaceId) {
                        createThreadModal.classList.add('space-mode');
                        spaceIdInput.value = spaceId;
                        categorySelectWrapper.classList.add('d-none');
                        categorySelect.removeAttribute('required');

                        if (typeArticle) {
                            typeArticle.checked = true;
                            typeArticle.dispatchEvent(new Event('change'));
                        }
                        formatSelector.classList.add('d-none');
                        threadContent.placeholder = "Tulis materi atau diskusi lengkap di sini...";
                        threadTitleContainer.classList.remove('d-none');
                    } else {
                        createThreadModal.classList.remove('space-mode');
                        spaceIdInput.value = '';
                        if (typeThread) {
                            typeThread.checked = true;
                            typeThread.dispatchEvent(new Event('change'));
                        }
                        categorySelectWrapper.classList.remove('d-none');
                        categorySelect.setAttribute('required', 'required');
                        formatSelector.classList.remove('d-none');
                        threadContent.placeholder = "Apa yang sedang kamu pikirkan?";
                    }
                    updateCounter();
                });

                const typeRadios = document.querySelectorAll('input[name="thread_type"]');
                typeRadios.forEach(radio => {
                    radio.addEventListener('change', (e) => {
                        updateCounter();
                        if (spaceIdInput.value) return;

                        if (e.target.value === 'article') {
                            threadTitleContainer.classList.remove('d-none');
                        } else {
                            threadTitleContainer.classList.add('d-none');
                        }
                    });
                });
            }

            // Toolbar Buttons
            if (pollBtn) {
                pollBtn.addEventListener('click', () => {
                    pollSection.classList.toggle('d-none');
                    videoSection.classList.add('d-none');
                });
            }

            if (videoBtn) {
                videoBtn.addEventListener('click', () => {
                    videoSection.classList.toggle('d-none');
                    pollSection.classList.add('d-none');
                });
            }

            if (tagBtn) {
                tagBtn.addEventListener('click', () => {
                    tagSection.classList.toggle('d-none');
                });
            }

            if (codeBtn) {
                codeBtn.addEventListener('click', () => {
                    const start = threadContent.selectionStart;
                    const end = threadContent.selectionEnd;
                    const text = threadContent.value;
                    const before = text.substring(0, start);
                    const after = text.substring(end, text.length);
                    const selection = text.substring(start, end);

                    const codeBlock = `\n\`\`\`\n${selection || '// Tulis kode di sini...'}\n\`\`\`\n`;

                    threadContent.value = before + codeBlock + after;
                    const newPos = start + codeBlock.length;
                    threadContent.setSelectionRange(newPos, newPos);
                    threadContent.focus();
                    updateCounter();
                });
            }

            // Poll Options logic
            const addOptionBtn = document.getElementById('addOptionBtn');
            const pollOptionsContainer = document.getElementById('pollOptionsContainer');
            const removePollBtn = document.getElementById('removePollBtn');

            if (addOptionBtn && pollOptionsContainer) {
                addOptionBtn.addEventListener('click', () => {
                    const count = pollOptionsContainer.querySelectorAll('input').length + 1;
                    if (count <= 5) {
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.name = 'poll_options[]';
                        input.className = 'form-control form-control-sm mb-2 rounded-3 border-0 bg-white shadow-sm';
                        input.placeholder = `Opsi ${count}`;
                        pollOptionsContainer.appendChild(input);
                    }
                    if (count === 5) addOptionBtn.classList.add('d-none');
                });
            }

            if (removePollBtn && pollOptionsContainer) {
                removePollBtn.addEventListener('click', () => {
                    pollSection.classList.add('d-none');
                    pollOptionsContainer.innerHTML = `
                                    <input type="text" name="poll_options[]" class="form-control form-control-sm mb-2 rounded-3 border-0 bg-white shadow-sm" placeholder="Opsi 1">
                                    <input type="text" name="poll_options[]" class="form-control form-control-sm mb-2 rounded-3 border-0 bg-white shadow-sm" placeholder="Opsi 2">
                                `;
                    if (addOptionBtn) addOptionBtn.classList.remove('d-none');
                });
            }

            const removeVideoBtn = document.getElementById('removeVideoBtn');
            if (removeVideoBtn && videoSection) {
                removeVideoBtn.addEventListener('click', () => {
                    videoSection.classList.add('d-none');
                    videoUrlInput.value = '';
                    urlPreviewArea.classList.add('d-none');
                    youtubePreview.src = '';
                });
            }

        });
    </script>
@endauth