<div class="modal fade" id="eventViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden border-top border-primary border-5">
            <div class="modal-header border-0 pb-0 px-4 pt-4 position-absolute end-0 top-0" style="z-index: 9999;">
                <button type="button" class="btn-close bg-white rounded-circle p-2 shadow-sm" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="eventViewModalBody">
                <div class="text-center py-5" id="eventLoader">
                    <div class="spinner-grow text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted fw-bold">Memuat detail acara...</p>
                </div>
                <div id="eventDetailContent" class="fade-in d-none">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #rsvpActions {
        transition: all 0.3s ease;
    }
</style>

<script>
    let currentEventUuid = null;

    function openEventDetail(uuid) {
        currentEventUuid = uuid;
        const loader = document.getElementById('eventLoader');
        const content = document.getElementById('eventDetailContent');

        loader.classList.remove('d-none');
        content.classList.add('d-none');

        // Open modal first
        let modalElement = document.getElementById('eventViewModal');
        let modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalElement);
        }
        modalInstance.show();

        // Fetch data
        fetch(`/spaces/{{ $space->slug }}/events/${uuid}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
                loader.classList.add('d-none');
                content.classList.remove('d-none');
            })
            .catch(error => {
                console.error('Error fetching event details:', error);
                content.innerHTML = '<div class="alert alert-danger">Gagal memuat detail acara. Silakan coba lagi.</div>';
                loader.classList.add('d-none');
                content.classList.remove('d-none');
            });
    }

    function updateRSVP(status) {
        if (!currentEventUuid) return;

        const rsvpActions = document.getElementById('rsvpActions');
        rsvpActions.style.opacity = '0.5';
        rsvpActions.style.pointerEvents = 'none';

        fetch(`/spaces/{{ $space->slug }}/events/${currentEventUuid}/rsvp`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: status })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('eventDetailContent').innerHTML = data.html;
                    // Success highlight or something? Standard bootstrap toast could be used if available
                }
            })
            .catch(error => {
                console.error('Error updating RSVP:', error);
                alert('Gagal memperbarui status kehadiran.');
            })
            .finally(() => {
                rsvpActions.style.opacity = '1';
                rsvpActions.style.pointerEvents = 'auto';
            });
    }

    function openNestedModal(modalId) {
        // Get the nested modal element from the AJAX-loaded content
        const nestedModal = document.getElementById(modalId);
        if (!nestedModal) {
            console.error('Modal not found:', modalId);
            return;
        }

        // Create and show the modal using Bootstrap
        const modal = new bootstrap.Modal(nestedModal, {
            backdrop: true,
            keyboard: true
        });
        modal.show();
    }
</script>