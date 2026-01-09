{{-- Shadow Ban Modal --}}
<div class="modal fade" id="shadowBanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Shadow Ban User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shadowBanForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-start gap-2 rounded-4 border-0 mb-4 bg-opacity-10"
                        style="background-color: rgba(255, 193, 7, 0.1);">
                        <span class="material-icons text-warning">info</span>
                        <div class="small">
                            <strong>Shadow Ban</strong> membuat user tetap bisa posting, tapi postingannya tidak
                            terlihat oleh orang lain. User tidak akan tahu bahwa mereka di-shadow ban.
                        </div>
                    </div>

                    <p class="mb-4">
                        Berikan shadow ban kepada <strong id="modalUserName">User</strong> (<span
                            id="modalUserUsername">@username</span>)?
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Durasi Shadow Ban</label>
                        <select name="duration" class="form-select rounded-3 p-2 bg-light border-0 shadow-none"
                            required>
                            <option value="1">1 Hari</option>
                            <option value="3">3 Hari</option>
                            <option value="7" selected>7 Hari</option>
                            <option value="30">30 Hari</option>
                            <option value="permanent">Permanen</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <span class="material-icons align-middle me-1" style="font-size: 18px;">visibility_off</span>
                        Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('shadowBanModal');
        if (!modal) return;

        const form = document.getElementById('shadowBanForm');
        const userName = document.getElementById('modalUserName');
        const userUsername = document.getElementById('modalUserUsername');

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const name = button.getAttribute('data-user-name');
            const username = button.getAttribute('data-user-username');

            form.action = `/admin/users/${userId}/shadow-ban`;
            userName.textContent = name;
            userUsername.textContent = '@' + username;
        });
    });
</script>