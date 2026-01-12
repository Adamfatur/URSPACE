@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <a href="{{ route('home') }}"
            class="text-decoration-none text-muted d-flex align-items-center gap-1 hover-primary transition-all">
            <span class="material-icons">arrow_back</span> Kembali ke Beranda
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0 p-4 mb-4">
        <h4 class="fw-bold mb-4">Edit Profil</h4>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Profile Picture --}}
            <div class="text-center mb-4">
                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center mx-auto mb-3"
                    style="width: 100px; height: 100px; overflow:hidden;">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-100 h-100 object-fit-cover">
                </div>
                <input type="file" name="avatar" id="avatar" class="form-control form-control-sm w-auto mx-auto" accept="image/*">
            </div>

            {{-- Basic Info Section --}}
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                <span class="material-icons align-middle me-2">person</span>Informasi Dasar
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control rounded-3 bg-light" value="{{ $user->username }}" disabled>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Headline</label>
                    <input type="text" name="headline" class="form-control rounded-3" value="{{ old('headline', $user->headline) }}" placeholder="Contoh: Mahasiswa Teknik Informatika | Web Developer">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Bio</label>
                    <textarea name="bio" class="form-control rounded-3" rows="3" placeholder="Ceritakan tentang dirimu...">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Lokasi</label>
                    <input type="text" name="location" class="form-control rounded-3" value="{{ old('location', $user->location) }}" placeholder="Contoh: Pekanbaru, Riau">
                </div>
            </div>

            {{-- Academic Info Section --}}
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                <span class="material-icons align-middle me-2">school</span>Informasi Akademik
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">NIM <span class="text-danger">*</span></label>
                    <input type="text" name="nim" class="form-control rounded-3" value="{{ old('nim', $user->nim) }}" placeholder="Contoh: 2101082031">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Angkatan <span class="text-danger">*</span></label>
                    <input type="number" name="angkatan" class="form-control rounded-3" value="{{ old('angkatan', $user->angkatan) }}" placeholder="Contoh: 2021" min="1990" max="2100">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Program Studi <span class="text-danger">*</span></label>
                    <input type="text" name="program_studi" class="form-control rounded-3" value="{{ old('program_studi', $user->program_studi) }}" placeholder="Contoh: Teknik Informatika">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Fakultas</label>
                    <input type="text" name="fakultas" class="form-control rounded-3" value="{{ old('fakultas', $user->fakultas) }}" placeholder="Contoh: Teknik">
                </div>
            </div>

            {{-- Social Links Section --}}
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                <span class="material-icons align-middle me-2">link</span>Tautan Sosial
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Website</label>
                    <input type="url" name="website" class="form-control rounded-3" value="{{ old('website', $user->website) }}" placeholder="https://...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">LinkedIn</label>
                    <input type="url" name="linkedin_url" class="form-control rounded-3" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">GitHub</label>
                    <input type="url" name="github_url" class="form-control rounded-3" value="{{ old('github_url', $user->github_url) }}" placeholder="https://github.com/...">
                </div>
            </div>

            {{-- Open to Work Section --}}
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                <span class="material-icons align-middle me-2">work</span>Status Pekerjaan
            </h5>
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="is_open_to_work" id="openToWork" value="1" {{ $user->is_open_to_work ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="openToWork">
                    <span class="badge bg-success me-2">Open to Work</span>
                    Saya sedang mencari kesempatan kerja/magang
                </label>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                <span class="material-icons align-middle me-1">save</span> Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Experience Section --}}
    <div class="card shadow-sm rounded-4 border-0 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <span class="material-icons align-middle me-2 text-primary">work_outline</span>Pengalaman Kerja
            </h5>
            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                <span class="material-icons align-middle small">add</span> Tambah
            </button>
        </div>

        @forelse($user->experiences as $exp)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $exp->title }}</h6>
                        <div class="text-muted">{{ $exp->company }} @if($exp->employment_type)· {{ $exp->employment_type }}@endif</div>
                        <div class="text-muted small">
                            {{ $exp->start_date->format('M Y') }} - {{ $exp->is_current ? 'Sekarang' : $exp->end_date?->format('M Y') }}
                            @if($exp->location)· {{ $exp->location }}@endif
                        </div>
                        @if($exp->description)
                            <p class="mt-2 mb-0 small text-dark">{{ $exp->description }}</p>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" 
                            data-bs-toggle="modal" data-bs-target="#deleteExpModal{{ $exp->id }}">
                            <span class="material-icons small">delete</span>
                        </button>
                    </div>

                    {{-- Delete Experience Modal --}}
                    <div class="modal fade" id="deleteExpModal{{ $exp->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-body text-center p-4">
                                    <div class="text-danger mb-3">
                                        <span class="material-icons" style="font-size: 48px;">delete_outline</span>
                                    </div>
                                    <h5 class="fw-bold">Hapus Pengalaman?</h5>
                                    <p class="text-muted small">Anda yakin ingin menghapus pengalaman di <strong>{{ $exp->company }}</strong>?</p>
                                    <div class="d-grid gap-2 mt-4">
                                        <form action="{{ route('profile.experience.destroy', $exp) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100">Ya, Hapus</button>
                                        </form>
                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted small mb-0">Belum ada pengalaman kerja.</p>
        @endforelse
    </div>

    {{-- Education Section --}}
    <div class="card shadow-sm rounded-4 border-0 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <span class="material-icons align-middle me-2 text-primary">school</span>Pendidikan
            </h5>
            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                <span class="material-icons align-middle small">add</span> Tambah
            </button>
        </div>

        @forelse($user->educations as $edu)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $edu->institution }}</h6>
                        <div class="text-muted">{{ $edu->degree }} @if($edu->field_of_study)- {{ $edu->field_of_study }}@endif</div>
                        <div class="text-muted small">{{ $edu->start_year }} - {{ $edu->is_current ? 'Sekarang' : $edu->end_year }}</div>
                        @if($edu->activities)
                            <p class="mt-2 mb-0 small text-dark">{{ $edu->activities }}</p>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" 
                            data-bs-toggle="modal" data-bs-target="#deleteEduModal{{ $edu->id }}">
                            <span class="material-icons small">delete</span>
                        </button>
                    </div>

                    {{-- Delete Education Modal --}}
                    <div class="modal fade" id="deleteEduModal{{ $edu->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-body text-center p-4">
                                    <div class="text-danger mb-3">
                                        <span class="material-icons" style="font-size: 48px;">school</span>
                                    </div>
                                    <h5 class="fw-bold">Hapus Pendidikan?</h5>
                                    <p class="text-muted small">Hapus riwayat pendidikan dari <strong>{{ $edu->institution }}</strong>?</p>
                                    <div class="d-grid gap-2 mt-4">
                                        <form action="{{ route('profile.education.destroy', $edu) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100">Ya, Hapus</button>
                                        </form>
                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted small mb-0">Belum ada pendidikan.</p>
        @endforelse
    </div>

    {{-- Skills Section --}}
    <div class="card shadow-sm rounded-4 border-0 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <span class="material-icons align-middle me-2 text-primary">psychology</span>Keahlian
            </h5>
        </div>

        <form action="{{ route('profile.skill.store') }}" method="POST" class="d-flex gap-2 mb-3">
            @csrf
            <input type="text" name="name" class="form-control form-control-sm rounded-pill" placeholder="Tambah skill..." required>
            <button class="btn btn-sm btn-primary rounded-pill px-3">Tambah</button>
        </form>

        <div class="d-flex flex-wrap gap-2">
            @forelse($user->skills as $skill)
                <form action="{{ route('profile.skill.destroy', $skill) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-secondary rounded-pill d-flex align-items-center gap-1">
                        {{ $skill->name }}
                        <span class="material-icons small text-danger">close</span>
                    </button>
                </form>
            @empty
                <p class="text-muted small mb-0">Belum ada keahlian.</p>
            @endforelse
        </div>
    </div>

    {{-- Certifications Section --}}
    <div class="card shadow-sm rounded-4 border-0 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <span class="material-icons align-middle me-2 text-primary">verified</span>Sertifikasi
            </h5>
            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
                <span class="material-icons align-middle small">add</span> Tambah
            </button>
        </div>

        @forelse($user->certifications as $cert)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $cert->name }}</h6>
                        <div class="text-muted">{{ $cert->issuer }}</div>
                        <div class="text-muted small">
                            @if($cert->issue_date)Diterbitkan {{ $cert->issue_date->format('M Y') }}@endif
                            @if($cert->expiry_date) · Berlaku sampai {{ $cert->expiry_date->format('M Y') }}@endif
                        </div>
                        @if($cert->credential_url)
                            <a href="{{ $cert->credential_url }}" target="_blank" class="text-primary small">Lihat Kredensial</a>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" 
                            data-bs-toggle="modal" data-bs-target="#deleteCertModal{{ $cert->id }}">
                            <span class="material-icons small">delete</span>
                        </button>
                    </div>

                    {{-- Delete Certification Modal --}}
                    <div class="modal fade" id="deleteCertModal{{ $cert->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-body text-center p-4">
                                    <div class="text-danger mb-3">
                                        <span class="material-icons" style="font-size: 48px;">verified</span>
                                    </div>
                                    <h5 class="fw-bold">Hapus Sertifikasi?</h5>
                                    <p class="text-muted small">Hapus sertifikat <strong>{{ $cert->name }}</strong>?</p>
                                    <div class="d-grid gap-2 mt-4">
                                        <form action="{{ route('profile.certification.destroy', $cert) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100">Ya, Hapus</button>
                                        </form>
                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted small mb-0">Belum ada sertifikasi.</p>
        @endforelse
    </div>

    {{-- Add Experience Modal --}}
    <div class="modal fade" id="addExperienceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Tambah Pengalaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('profile.experience.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="company" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipe Pekerjaan</label>
                            <select name="employment_type" class="form-select">
                                <option value="">Pilih...</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Internship">Internship</option>
                                <option value="Freelance">Freelance</option>
                                <option value="Contract">Contract</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_current" value="1" class="form-check-input" id="expCurrent">
                            <label class="form-check-label" for="expCurrent">Saya masih bekerja di sini</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Education Modal --}}
    <div class="modal fade" id="addEducationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Tambah Pendidikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('profile.education.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Institusi <span class="text-danger">*</span></label>
                            <input type="text" name="institution" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gelar</label>
                            <select name="degree" class="form-select">
                                <option value="">Pilih...</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bidang Studi</label>
                            <input type="text" name="field_of_study" class="form-control">
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Tahun Mulai <span class="text-danger">*</span></label>
                                <input type="number" name="start_year" class="form-control" min="1990" max="2100" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Tahun Selesai</label>
                                <input type="number" name="end_year" class="form-control" min="1990" max="2100">
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_current" value="1" class="form-check-input" id="eduCurrent">
                            <label class="form-check-label" for="eduCurrent">Saya masih bersekolah di sini</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kegiatan & Organisasi</label>
                            <textarea name="activities" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Certification Modal --}}
    <div class="modal fade" id="addCertificationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Tambah Sertifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('profile.certification.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Sertifikasi <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Penerbit <span class="text-danger">*</span></label>
                            <input type="text" name="issuer" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Tanggal Terbit</label>
                                <input type="date" name="issue_date" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Tanggal Kedaluwarsa</label>
                                <input type="date" name="expiry_date" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Kredensial</label>
                            <input type="text" name="credential_id" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">URL Kredensial</label>
                            <input type="url" name="credential_url" class="form-control" placeholder="https://...">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection