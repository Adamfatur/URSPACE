/**
 * Shared Forum Interaction Logic
 * Used by home.blade.php (User Feed) and threads/show.blade.php (Thread Detail)
 */

window.Forum = {
    // Helper to get CSRF token
    getCsrfToken: function () {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    },

    // Show Toast Notification
    showToast: function (message, type = 'success') {
        const toastEl = document.getElementById('liveToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        if (!toastEl || !toastMessage || !toastIcon) {
            console.warn('Toast elements not found');
            alert(message);
            return;
        }

        toastMessage.textContent = message;

        // Style based on type
        toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-secondary', 'bg-sage-dark', 'text-white');
        if (type === 'success') {
            toastEl.classList.add('bg-success', 'text-white');
            toastIcon.textContent = 'check_circle';
        } else if (type === 'danger' || type === 'error') {
            toastEl.classList.add('bg-danger', 'text-white');
            toastIcon.textContent = 'error';
        } else {
            toastEl.classList.add('bg-sage-dark', 'text-white');
            toastIcon.textContent = 'info';
        }

        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
    },

    // Toggle Like Logic
    toggleLike: async function (btn, type, id) {
        const url = `/likes/${type}/${id}`;
        const icon = btn.querySelector('.material-icons');
        const countSpan = btn.querySelector('.count');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                btn.classList.toggle('active', data.liked);
                icon.textContent = data.liked ? 'favorite' : 'favorite_border';
                if (countSpan) countSpan.textContent = data.likes_count;
                this.showToast(data.liked ? 'Berhasil menyukai!' : 'Batal menyukai.');
            }
        } catch (error) {
            console.error('Like error:', error);
            this.showToast('Gagal memproses like.', 'danger');
        }
    },

    // Submit Comment Logic
    submitComment: async function (threadId, content, parentId = null) {
        try {
            const response = await fetch(`/threads/${threadId}/posts`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content: content,
                    parent_id: parentId
                })
            });

            const contentType = response.headers.get('content-type');
            let data = null;

            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            }

            if (!response.ok) {
                if (data && data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join('\n');
                    this.showToast('Validasi Gagal: ' + errorMessages, 'danger');
                } else {
                    this.showToast('Gagal: ' + (data ? data.message : 'Terjadi kesalahan sistem (' + response.status + ')'), 'danger');
                }
                return false;
            }

            return data;
        } catch (error) {
            console.error('Reply Error:', error);
            this.showToast('Terjadi kesalahan: ' + error.message, 'danger');
            return false;
        }
    },

    // Submit Report Logic
    submitReport: async function (type, id, reason) {
        try {
            const response = await fetch(`/report/${type}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ reason: reason })
            });

            const data = await response.json();
            if (data.success) {
                this.showToast(data.message);
                return true;
            } else {
                this.showToast('Gagal mengirim laporan.', 'danger');
                return false;
            }
        } catch (error) {
            this.showToast('Gagal mengirim laporan.', 'danger');
            return false;
        }
    },

    // Toggle Pin Logic
    togglePin: async function (btn, id) {
        try {
            const response = await fetch(`/posts/${id}/pin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': Forum.getCsrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                Forum.showToast(errorData.message || 'Gagal memproses pin.', 'warning');
                return;
            }

            const data = await response.json();
            if (data.success) {
                Forum.showToast(data.message);
                const container = document.getElementById('post-' + id);
                if (container) {
                    if (data.is_pinned) {
                        container.style.backgroundColor = '#f0f9f0';
                        container.classList.add('border-primary');
                        container.classList.remove('border-0', 'bg-white');
                    } else {
                        container.style.backgroundColor = '';
                        container.classList.remove('border-primary');
                        container.classList.add('border-0', 'bg-white');
                    }
                }
                setTimeout(() => location.reload(), 500);
            } else {
                Forum.showToast(data.message, 'warning');
            }
        } catch (error) {
            console.error('Pin error:', error);
            Forum.showToast('Error: ' + error.message, 'danger');
        }
    },

    // Toggle Hide Logic
    toggleHide: async function (btn, id) {
        try {
            const response = await fetch(`/posts/${id}/hide`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                this.showToast(data.message);
                setTimeout(() => location.reload(), 500);
            }
        } catch (error) {
            this.showToast('Gagal mengubah status komentar.', 'danger');
        }
    },

    // Delete Post Logic
    deletePost: async function (id) {
        if (!confirm('Hapus komentar ini?')) return;

        try {
            const response = await fetch(`/posts/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                const element = document.getElementById(`post-${id}`);
                if (element) element.remove();
                this.showToast('Komentar dihapus.');
            }
        } catch (error) {
            this.showToast('Gagal menghapus komentar.', 'danger');
        }
    },

    // Confirm Delete Logic
    copyLink: function (url) {
        if (!navigator.clipboard) {
            this.showToast('Browser tidak mendukung copy otomatis.', 'warning');
            return;
        }
        navigator.clipboard.writeText(url).then(() => {
            this.showToast('Tautan disalin ke clipboard!');
        }).catch(() => {
            this.showToast('Gagal menyalin tautan.', 'danger');
        });
    },

    guestAction: function () {
        this.showToast('Silakan login untuk melakukan aksi ini.', 'info');
        setTimeout(() => {
            window.location.href = '/login';
        }, 1000);
    },

    deleteThread: async function (id) {
        try {
            const response = await fetch(`/threads/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                this.showToast('Thread berhasil dihapus.');
                setTimeout(() => location.reload(), 1000);
            }
        } catch (error) {
            this.showToast('Gagal menghapus thread.', 'danger');
        }
    },

    openShareModal: function (url, title = 'Forum UR') {
        const shareWA = document.getElementById('shareWA');
        const shareX = document.getElementById('shareX');
        const shareFB = document.getElementById('shareFB');
        const shareUrlInput = document.getElementById('shareUrlInput');
        const shareModalEl = document.getElementById('shareModal');

        if (!shareModalEl) return;

        if (shareUrlInput) shareUrlInput.value = url;

        const encodedUrl = encodeURIComponent(url);
        const encodedTitle = encodeURIComponent(title);

        if (shareWA) shareWA.href = `https://api.whatsapp.com/send?text=${encodedTitle}%20${encodedUrl}`;
        if (shareX) shareX.href = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`;
        if (shareFB) shareFB.href = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;

        const modal = new bootstrap.Modal(shareModalEl);
        modal.show();
    },

    openImageModal: function (imageUrl) {
        const modalEl = document.getElementById('imageViewerModal');
        const img = document.getElementById('imageViewerImg');
        const downloadBtn = document.getElementById('imageViewerDownload');

        if (modalEl && img) {
            img.src = imageUrl;
            if (downloadBtn) {
                downloadBtn.href = imageUrl;
                // Force download attribute to work by ensuring same-origin or blob where possible, 
                // but for now simple href is enough for user request.
            }

            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    },

    // Code Copy Feature
    initCodeCopy: function () {
        const preBlocks = document.querySelectorAll('.markdown-content pre');

        preBlocks.forEach(pre => {
            // Avoid duplicate initialization
            if (pre.parentElement.classList.contains('code-block-wrapper')) return;

            const code = pre.querySelector('code');
            if (!code) return;

            // 1. Determine Language
            let lang = 'code';
            const langClass = Array.from(code.classList).find(c => c.startsWith('language-'));
            if (langClass) {
                lang = langClass.replace('language-', '');
            } else if (code.classList.contains('hljs')) {
                // Catch-all for highlight.js auto-detected but no explicit class
                lang = 'auto';
            }

            // 2. Create Wrapper & Header
            const wrapper = document.createElement('div');
            wrapper.className = 'code-block-wrapper';

            const header = document.createElement('div');
            header.className = 'code-header';
            header.innerHTML = `
                <span class="code-lang">${lang}</span>
                <button type="button" class="code-copy-btn">
                    <span class="material-icons">content_copy</span>
                    <span class="btn-text">Copy</span>
                </button>
            `;

            // 3. Setup Structure
            pre.parentNode.insertBefore(wrapper, pre);
            wrapper.appendChild(header);
            wrapper.appendChild(pre);

            // 4. Copy Logic
            const copyBtn = header.querySelector('.code-copy-btn');
            const btnText = copyBtn.querySelector('.btn-text');
            const btnIcon = copyBtn.querySelector('.material-icons');

            copyBtn.addEventListener('click', () => {
                const textToCopy = code.innerText;

                navigator.clipboard.writeText(textToCopy).then(() => {
                    copyBtn.classList.add('active');
                    btnText.textContent = 'Copied!';
                    btnIcon.textContent = 'check';

                    // Use Forum.showToast if you want local notification too
                    // this.showToast('Kode disalin!', 'success');

                    setTimeout(() => {
                        copyBtn.classList.remove('active');
                        btnText.textContent = 'Copy';
                        btnIcon.textContent = 'content_copy';
                    }, 2000);
                }).catch(err => {
                    console.error('Copy failed:', err);
                    this.showToast('Gagal menyalin kode.', 'danger');
                });
            });
        });
    },

    // Submit Poll Vote
    submitVote: async function (threadUuid, optionId) {
        console.log('Forum.submitVote called', threadUuid, optionId);
        try {
            const response = await fetch(`/threads/${threadUuid}/vote`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ poll_option_id: optionId })
            });

            const data = await response.json();
            if (data.success) {
                this.showToast(data.message);
                // Update poll UI
                const wrapper = document.getElementById(`poll-container-${threadUuid}`);
                if (wrapper && data.html) {
                    wrapper.outerHTML = data.html;
                } else {
                    setTimeout(() => location.reload(), 500);
                }
            } else {
                this.showToast(data.message || 'Gagal mengirim suara.', 'danger');
            }
        } catch (error) {
            console.error('Vote Error:', error);
            this.showToast('Gagal memproses suara.', 'danger');
        }
    },

    // Dismiss global announcement (session-based)
    dismissAnnouncement: function (id) {
        const dismissedIds = JSON.parse(sessionStorage.getItem('dismissedAnnouncements') || '[]');
        if (!dismissedIds.includes(id)) {
            dismissedIds.push(id);
            sessionStorage.setItem('dismissedAnnouncements', JSON.stringify(dismissedIds));
        }
        const el = document.querySelector(`[data-announcement-id="${id}"]`);
        if (el) {
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-10px)';
            setTimeout(() => el.remove(), 300);
        }
    },

    // Hide already-dismissed announcements on page load
    initDismissedAnnouncements: function () {
        const dismissedIds = JSON.parse(sessionStorage.getItem('dismissedAnnouncements') || '[]');
        dismissedIds.forEach(id => {
            const el = document.querySelector(`[data-announcement-id="${id}"]`);
            if (el) el.remove();
        });
    }
};

// Auto-init on load
document.addEventListener('DOMContentLoaded', () => {
    if (window.Forum && window.Forum.initCodeCopy) {
        window.Forum.initCodeCopy();
    }
    if (window.Forum && window.Forum.initDismissedAnnouncements) {
        window.Forum.initDismissedAnnouncements();
    }
});
