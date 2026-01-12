@extends('layouts.app')

@section('content')
    <div class="container-fluid px-0 px-md-3">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <a href="{{ route('spaces.index') }}"
                        class="btn btn-light rounded-pill px-3 shadow-none border-0 text-muted d-flex align-items-center gap-1">
                        <span class="material-icons">arrow_back</span> Kembali
                    </a>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <div class="text-center mb-5">
                        <h1 class="fw-black text-sage-900 mb-2">Bangun URSpace Kamu</h1>
                        <p class="text-muted mx-auto" style="max-width: 450px;">
                            Ciptakan komunitas yang positif dan inklusif untuk berdiskusi tentang topik yang kamu cintai.
                        </p>
                    </div>

                    <form action="{{ route('spaces.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold text-sage-900">Nama URSpace</label>
                            <input type="text" name="name" class="form-control rounded-4 p-3 bg-light border-0"
                                placeholder="Contoh: Alumni Teknik Informatika 2023" required>
                            <div class="form-text px-2">Nama unik untuk komunitas Anda yang akan tampil di URL.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-sage-900">Deskripsi URSpace</label>
                            <textarea name="description" rows="4" class="form-control rounded-4 p-3 bg-light border-0"
                                placeholder="Jelaskan tujuan dan aturan di komunitas ini agar anggota baru paham maksudnya..."
                                required></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-sage-900">Gambar Sampul (Opsional)</label>
                            <div class="input-group">
                                <input type="file" name="cover_image" class="form-control rounded-4 p-3 bg-light border-0"
                                    accept="image/*">
                            </div>
                            <div class="form-text px-2">Format: JPG, PNG. Rekomendasi 1200x400px (Max 2MB).</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-sage-900">Visibilitas URSpace</label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline bg-light rounded-4 p-3 px-4 flex-grow-1 m-0">
                                    <input class="form-check-input" type="radio" name="is_private" id="is_public" value="0"
                                        checked>
                                    <label class="form-check-label fw-bold" for="is_public">
                                        <span class="material-icons text-sage-500 align-middle me-1"
                                            style="font-size: 18px;">public</span>
                                        Publik
                                    </label>
                                    <div class="form-text small text-muted mt-1">Siapa pun bisa melihat dan bergabung.</div>
                                </div>
                                <div class="form-check form-check-inline bg-light rounded-4 p-3 px-4 flex-grow-1 m-0">
                                    <input class="form-check-input" type="radio" name="is_private" id="is_private"
                                        value="1">
                                    <label class="form-check-label fw-bold" for="is_private">
                                        <span class="material-icons text-sage-500 align-middle me-1"
                                            style="font-size: 18px;">lock</span>
                                        Privat
                                    </label>
                                    <div class="form-text small text-muted mt-1">Anggota harus diundang atau disetujui.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 rounded-4 d-flex align-items-center gap-2 mb-4"
                            role="alert">
                            <span class="material-icons text-warning">schedule</span>
                            <small>Pengajuan URSpace baru akan ditinjau oleh admin dalam waktu <strong>maksimal 1x24
                                    jam</strong>.</small>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm">
                                Ajukan URSpace Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .fw-black {
            font-weight: 800;
        }

        .text-sage-900 {
            color: #1f2c1f;
        }

        .form-control:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 4px rgba(94, 139, 94, 0.1) !important;
        }
    </style>
@endpush