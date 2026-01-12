<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Comment Modal logic
        const commentModal = document.getElementById('commentModal');
        const commentForm = document.getElementById('commentForm');
        const commentContent = document.getElementById('commentContent');
        const commentCharCount = document.getElementById('commentCharCount');
        let activeThreadId = null;
        let activeParentId = null;

        if (commentModal) {
            commentModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                activeThreadId = button.getAttribute('data-thread-id');
                activeParentId = button.getAttribute('data-parent-id') || null; // Support nested replies
                const username = button.getAttribute('data-thread-username');

                const targetUserEl = document.getElementById('commentTargetUser');
                if (targetUserEl) targetUserEl.textContent = username;

                if (commentContent) {
                    commentContent.value = '';
                    // Focus after small delay
                    setTimeout(() => commentContent.focus(), 500);
                }
                if (commentCharCount) commentCharCount.textContent = '0';
            });

            if (commentContent && commentCharCount) {
                commentContent.addEventListener('input', () => {
                    commentCharCount.textContent = commentContent.value.length;
                });
            }

            if (commentForm) {
                commentForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!activeThreadId) return;

                    const submitBtn = commentForm.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;

                    const result = await Forum.submitComment(activeThreadId, commentContent.value, activeParentId);

                    if (result && result.success) {
                        const modalInstance = bootstrap.Modal.getInstance(commentModal);
                        if (modalInstance) modalInstance.hide();

                        // Update count in UI if exists (Feed View)
                        const interactionBtn = document.querySelector(`[data-thread-id="${activeThreadId}"]`);
                        if (interactionBtn) {
                            const countElement = interactionBtn.querySelector('.count');
                            if (countElement) countElement.textContent = result.posts_count;
                        }

                        // Show success modal
                        const successModalEl = document.getElementById('commentSuccessModal');
                        if (successModalEl) {
                            const successModal = new bootstrap.Modal(successModalEl);
                            successModal.show();

                            const viewBtn = document.getElementById('viewFullThreadBtn');
                            if (viewBtn) viewBtn.href = `/threads/${activeThreadId}`;
                        } else {
                            // If no success modal (fallback), just reload or toast
                            Forum.showToast('Balasan terkirim!');
                            setTimeout(() => location.reload(), 1000);
                        }
                    } else {
                        // Error handled in Forum.submitComment
                    }
                    submitBtn.disabled = false;
                });
            }
        }

        // Report Modal logic
        const reportModal = document.getElementById('reportModal');
        const reportForm = document.getElementById('reportForm');
        let reportTarget = { id: null, type: null };

        if (reportModal) {
            reportModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                reportTarget.id = button.getAttribute('data-id');
                reportTarget.type = button.getAttribute('data-type');
            });

            if (reportForm) {
                reportForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const reason = document.getElementById('reportReason').value;
                    const extra = document.getElementById('reportExtra').value;
                    const fullReason = `${reason}: ${extra}`;

                    const submitBtn = reportForm.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;

                    const success = await Forum.submitReport(reportTarget.type, reportTarget.id, fullReason);
                    if (success) {
                        const modalInstance = bootstrap.Modal.getInstance(reportModal);
                        if (modalInstance) modalInstance.hide();
                    }
                    submitBtn.disabled = false;
                });
            }
        }

        // Delete Modal Logic
        const deleteModal = document.getElementById('confirmDeleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let deleteId = null;

        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                deleteId = event.relatedTarget.getAttribute('data-id');
            });

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', async () => {
                    if (!deleteId) return;
                    confirmDeleteBtn.disabled = true;
                    await Forum.deleteThread(deleteId);
                    confirmDeleteBtn.disabled = false;
                });
            }
        }

        // Edit Modal Logic
        const editModal = document.getElementById('editThreadModal');
        const editForm = document.getElementById('editThreadForm');
        const editThreadContent = document.getElementById('editThreadContent');
        const editThreadTitle = document.getElementById('editThreadTitle');
        const editTitleContainer = document.getElementById('editTitleContainer');
        const editCharCounter = document.getElementById('editCharCounter');
        const editCategorySelect = document.getElementById('editCategorySelect');
        const editTypeThread = document.getElementById('edit_type_thread');
        const editTypeArticle = document.getElementById('edit_type_article');
        const editCategorySelectWrapper = document.getElementById('editCategorySelectWrapper');

        const editPollBtn = document.getElementById('editPollBtn');
        const editVideoBtn = document.getElementById('editVideoBtn');
        const editCodeBtn = document.getElementById('editCodeBtn');

        const editPollSection = document.getElementById('editPollSection');
        const editVideoSection = document.getElementById('editVideoSection');

        const editPollOptionsContainer = document.getElementById('editPollOptionsContainer');
        const editVideoUrl = document.getElementById('editVideoUrl');

        const editFileInput = document.getElementById('editFileInput');
        const editMediaPreview = document.getElementById('editMediaPreview');

        let editId = null;

        if (editModal) {
            function updateEditCounter() {
                const isShortThread = editTypeThread && editTypeThread.checked;
                if (isShortThread) {
                    const remaining = 256 - editThreadContent.value.length;
                    if (editCharCounter) {
                        editCharCounter.textContent = remaining;
                        editCharCounter.classList.toggle('text-danger', remaining < 0);
                        editCharCounter.classList.remove('d-none');
                    }
                } else {
                    if (editCharCounter) editCharCounter.classList.add('d-none');
                }

                // Auto-resize
                editThreadContent.style.height = 'auto';
                editThreadContent.style.height = editThreadContent.scrollHeight + 'px';
            }

            if (editThreadContent) {
                editThreadContent.addEventListener('input', updateEditCounter);
            }

            // Video Preview for Edit
            const editVideoUrlInput = document.getElementById('editVideoUrl');
            const editUrlPreviewArea = document.getElementById('editUrlPreviewArea');
            const editYoutubePreview = document.getElementById('editYoutubePreview');

            function updateEditVideoPreview() {
                const url = editVideoUrlInput.value;
                const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
                const match = url.match(youtubeRegex);

                if (match && match[1]) {
                    editYoutubePreview.src = `https://www.youtube.com/embed/${match[1]}`;
                    editUrlPreviewArea.classList.remove('d-none');
                } else {
                    editUrlPreviewArea.classList.add('d-none');
                    editYoutubePreview.src = '';
                }
            }

            if (editVideoUrlInput) {
                editVideoUrlInput.addEventListener('input', updateEditVideoPreview);
            }

            // Tag Logic for Edit
            const editTagPills = document.querySelectorAll('.edit-tag-pill');
            const editSelectedTagsContainer = document.getElementById('editSelectedTagsContainer');

            function syncEditTagPills(tagIds) {
                editTagPills.forEach(pill => {
                    const id = pill.getAttribute('data-tag-id');
                    if (tagIds.includes(parseInt(id)) || tagIds.includes(id)) {
                        pill.classList.add('active');
                        addEditTagInput(id);
                    } else {
                        pill.classList.remove('active');
                    }
                });
            }

            function addEditTagInput(tagId) {
                if (!editSelectedTagsContainer.querySelector(`input[value="${tagId}"]`)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'tags[]';
                    input.value = tagId;
                    editSelectedTagsContainer.appendChild(input);
                }
            }

            editTagPills.forEach(pill => {
                pill.addEventListener('click', () => {
                    const tagId = pill.getAttribute('data-tag-id');
                    pill.classList.toggle('active');
                    const existingInput = editSelectedTagsContainer.querySelector(`input[value="${tagId}"]`);
                    if (existingInput) {
                        existingInput.remove();
                    } else {
                        addEditTagInput(tagId);
                    }
                });
            });

            const editTagBtn = document.getElementById('editTagBtn');
            const editTagSection = document.getElementById('editTagSection');
            if (editTagBtn) {
                editTagBtn.addEventListener('click', () => editTagSection.classList.toggle('d-none'));
            }

            if (editFileInput && editMediaPreview) {
                editFileInput.addEventListener('change', function () {
                    editMediaPreview.innerHTML = '';
                    const file = this.files[0];
                    if (file) {
                        const container = document.createElement('div');
                        container.className = 'edit-media-preview-container mb-3';

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'edit-media-preview-remove';
                        removeBtn.innerHTML = '<span class="material-icons fs-6">close</span>';
                        removeBtn.onclick = () => {
                            editFileInput.value = '';
                            editMediaPreview.innerHTML = '';
                        };

                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.className = 'w-100 rounded-4 border object-fit-cover shadow-sm';
                            img.style.maxHeight = '200px';
                            container.appendChild(img);
                        } else if (file.type.startsWith('video/')) {
                            const vid = document.createElement('video');
                            vid.src = URL.createObjectURL(file);
                            vid.className = 'w-100 rounded-4 border shadow-sm';
                            vid.style.maxHeight = '200px';
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
                        editMediaPreview.appendChild(container);

                        // Clear video URL if file selected
                        if (editVideoUrlInput) {
                            editVideoUrlInput.value = '';
                            updateEditVideoPreview();
                        }
                    }
                });
            }

            editModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                editId = btn.getAttribute('data-id');
                const format = btn.getAttribute('data-format');
                const title = btn.getAttribute('data-title');
                const content = btn.getAttribute('data-content');
                const categoryId = btn.getAttribute('data-category-id');
                const spaceId = btn.getAttribute('data-space-id');
                const videoUrl = btn.getAttribute('data-video-url');
                const tagsRaw = btn.getAttribute('data-tags');
                const pollOptionsRaw = btn.getAttribute('data-poll-options');

                // Reset Sections
                if (editPollSection) editPollSection.classList.add('d-none');
                if (editVideoSection) editVideoSection.classList.add('d-none');
                if (editTagSection) editTagSection.classList.add('d-none');
                if (editMediaPreview) editMediaPreview.innerHTML = '';
                if (editFileInput) editFileInput.value = '';
                if (editSelectedTagsContainer) editSelectedTagsContainer.innerHTML = '';

                // Populate Basic Info
                if (editThreadContent) editThreadContent.value = content || '';
                if (editThreadTitle) editThreadTitle.value = title || '';
                if (editCategorySelect) editCategorySelect.value = categoryId || '';

                // Handle Category/Space visibility
                if (editCategorySelectWrapper) {
                    if (spaceId && spaceId !== 'null' && spaceId !== '') {
                        editCategorySelectWrapper.classList.add('d-none');
                        if (editCategorySelect) editCategorySelect.removeAttribute('required');
                    } else {
                        editCategorySelectWrapper.classList.remove('d-none');
                        if (editCategorySelect) editCategorySelect.setAttribute('required', 'required');
                    }
                }

                // Handle Format Selection & Title visibility
                if (format === 'article') {
                    if (editTypeArticle) editTypeArticle.checked = true;
                    if (editTitleContainer) editTitleContainer.classList.remove('d-none');
                } else {
                    if (editTypeThread) editTypeThread.checked = true;
                    if (editTitleContainer) editTitleContainer.classList.add('d-none');
                }

                // If we are in a space, title is ALWAYS visible & format is Article
                if (spaceId && spaceId !== 'null' && spaceId !== '') {
                    if (editTypeArticle) editTypeArticle.checked = true;
                    if (editTitleContainer) editTitleContainer.classList.remove('d-none');
                    const formatSelector = document.getElementById('editFormatSelector');
                    if (formatSelector) formatSelector.classList.add('d-none');
                } else {
                    const formatSelector = document.getElementById('editFormatSelector');
                    if (formatSelector) formatSelector.classList.remove('d-none');
                }

                // Handle Image Display
                const imageUrl = btn.getAttribute('data-image');
                const editRemoveImage = document.getElementById('editRemoveImage');
                if (editRemoveImage) editRemoveImage.value = '0'; // Reset remove flag
                
                if (imageUrl && imageUrl.trim() !== '') {
                    if (editMediaPreview) {
                        editMediaPreview.innerHTML = `
                            <div class="edit-media-preview-container mt-3">
                                <img src="${imageUrl}" class="img-fluid rounded-3 w-100" style="max-height: 300px; object-fit: cover;">
                                <button type="button" class="edit-media-preview-remove" id="editRemoveImageBtn">
                                    <span class="material-icons">close</span>
                                </button>
                            </div>
                        `;
                        
                        // Add event listener to remove button
                        const removeBtn = document.getElementById('editRemoveImageBtn');
                        if (removeBtn) {
                            removeBtn.addEventListener('click', function() {
                                if (editMediaPreview) editMediaPreview.innerHTML = '';
                                if (editFileInput) editFileInput.value = '';
                                if (editRemoveImage) editRemoveImage.value = '1';
                            });
                        }
                    }
                }

                updateEditCounter();

                // Handle Video
                if (videoUrl) {
                    if (editVideoSection) editVideoSection.classList.remove('d-none');
                    if (editVideoUrlInput) editVideoUrlInput.value = videoUrl;
                    updateEditVideoPreview();
                } else if (editVideoUrlInput) {
                    editVideoUrlInput.value = '';
                    updateEditVideoPreview();
                }

                // Handle Poll Options
                if (editPollOptionsContainer) editPollOptionsContainer.innerHTML = '';
                if (pollOptionsRaw) {
                    try {
                        const options = JSON.parse(pollOptionsRaw);
                        if (options.length > 0) {
                            if (editPollSection) editPollSection.classList.remove('d-none');
                            options.forEach((opt, index) => {
                                const input = document.createElement('input');
                                input.type = 'text';
                                input.name = 'poll_options[]';
                                input.className = 'form-control form-control-sm mb-2 rounded-3 border-0 bg-white shadow-sm';
                                input.placeholder = `Opsi ${index + 1}`;
                                input.value = opt;
                                editPollOptionsContainer.appendChild(input);
                            });
                        }
                    } catch (e) { console.error('Error parsing poll options:', e); }
                }

                // Handle Tags
                if (tagsRaw) {
                    try {
                        const tagIds = JSON.parse(tagsRaw);
                        syncEditTagPills(tagIds);
                    } catch (e) { console.error('Error parsing tags:', e); }
                }
            });

            // Format Toggle logic
            const editTypeRadios = document.querySelectorAll('input[name="thread_type"][id^="edit_"]');
            editTypeRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    updateEditCounter();
                    const btn = document.querySelector(`[data-id="${editId}"]`);
                    const spaceId = btn ? btn.getAttribute('data-space-id') : null;
                    if (spaceId && spaceId !== 'null' && spaceId !== '') return;

                    if (e.target.value === 'article') {
                        if (editTitleContainer) editTitleContainer.classList.remove('d-none');
                    } else {
                        if (editTitleContainer) editTitleContainer.classList.add('d-none');
                    }
                });
            });

            // Toolbar Actions
            if (editPollBtn) {
                editPollBtn.addEventListener('click', () => {
                    editPollSection.classList.toggle('d-none');
                    editVideoSection.classList.add('d-none');

                    if (!editPollSection.classList.contains('d-none') && editPollOptionsContainer.children.length === 0) {
                        for (let i = 1; i <= 2; i++) {
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.name = 'poll_options[]';
                            input.className = 'form-control form-control-sm mb-2 rounded-3 border-0 bg-white shadow-sm';
                            input.placeholder = `Opsi ${i}`;
                            editPollOptionsContainer.appendChild(input);
                        }
                    }
                });
            }

            if (editVideoBtn) {
                editVideoBtn.addEventListener('click', () => {
                    editVideoSection.classList.toggle('d-none');
                    editPollSection.classList.add('d-none');
                });
            }

            if (editCodeBtn) {
                editCodeBtn.addEventListener('click', () => {
                    const start = editThreadContent.selectionStart;
                    const end = editThreadContent.selectionEnd;
                    const text = editThreadContent.value;
                    const before = text.substring(0, start);
                    const after = text.substring(end, text.length);
                    const selection = text.substring(start, end);

                    const codeBlock = `\n\`\`\`\n${selection || '// Tulis kode di sini...'}\n\`\`\`\n`;

                    editThreadContent.value = before + codeBlock + after;
                    const newPos = start + codeBlock.length;
                    editThreadContent.setSelectionRange(newPos, newPos);
                    editThreadContent.focus();
                    updateEditCounter();
                });
            }

            // Remove/Add logic for Poll
            const editAddOptionBtn = document.getElementById('editAddOptionBtn');
            if (editAddOptionBtn) {
                editAddOptionBtn.addEventListener('click', () => {
                    const count = editPollOptionsContainer.querySelectorAll('input').length + 1;
                    if (count <= 5) {
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.name = 'poll_options[]';
                        input.className = 'form-control form-control-sm mb-2 rounded-3 border-0 bg-white shadow-sm';
                        input.placeholder = `Opsi ${count}`;
                        editPollOptionsContainer.appendChild(input);
                    }
                    if (count === 5) editAddOptionBtn.classList.add('d-none');
                });
            }

            const editRemovePollBtn = document.getElementById('editRemovePollBtn');
            if (editRemovePollBtn) {
                editRemovePollBtn.addEventListener('click', () => {
                    editPollSection.classList.add('d-none');
                    editPollOptionsContainer.innerHTML = '';
                    if (editAddOptionBtn) editAddOptionBtn.classList.remove('d-none');
                });
            }

            const editRemoveVideoBtn = document.getElementById('editRemoveVideoBtn');
            if (editRemoveVideoBtn) {
                editRemoveVideoBtn.addEventListener('click', () => {
                    editVideoSection.classList.add('d-none');
                    editVideoUrlInput.value = '';
                    updateEditVideoPreview();
                });
            }

            if (editForm) {
                editForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!editId) return;

                    const submitBtn = editForm.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;

                    const formData = new FormData(editForm);
                    formData.append('_method', 'PUT');

                    try {
                        const response = await fetch(`/threads/${editId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': Forum.getCsrfToken(),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const result = await response.json();
                        if (result.success) {
                            Forum.showToast('Thread diperbarui!');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Forum.showToast(result.message || 'Gagal memperbarui thread.', 'danger');
                        }
                    } catch (error) {
                        console.error('Update Error:', error);
                        Forum.showToast('Terjadi kesalahan sistem.', 'danger');
                    } finally {
                        submitBtn.disabled = false;
                    }
                });
            }
        }

        // Enhance Markdown Code Blocks
        function enhanceCodeBlocks() {
            document.querySelectorAll('.markdown-content pre').forEach(pre => {
                if (pre.parentElement.classList.contains('code-block-wrapper')) return;

                const wrapper = document.createElement('div');
                wrapper.className = 'code-block-wrapper';

                const header = document.createElement('div');
                header.className = 'code-header';

                const code = pre.querySelector('code');
                let lang = 'code';
                if (code) {
                    const langMatch = code.className.match(/language-(\w+)/);
                    if (langMatch) lang = langMatch[1];
                }

                header.innerHTML = `
                    <span class="code-lang">${lang}</span>
                    <button type="button" class="code-copy-btn">
                        <span class="material-icons">content_copy</span>
                        Salin
                    </button>
                `;

                header.querySelector('.code-copy-btn').addEventListener('click', function () {
                    const text = pre.textContent;
                    navigator.clipboard.writeText(text).then(() => {
                        const btn = this;
                        const originalHtml = btn.innerHTML;
                        btn.innerHTML = '<span class="material-icons">done</span> Tersalin!';
                        setTimeout(() => btn.innerHTML = originalHtml, 2000);
                    });
                });

                pre.parentNode.insertBefore(wrapper, pre);
                wrapper.appendChild(header);
                wrapper.appendChild(pre);
            });
        }

        enhanceCodeBlocks();
        // Handle dynamic content (e.g. infinite scroll if any)
        const observer = new MutationObserver(enhanceCodeBlocks);
        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>