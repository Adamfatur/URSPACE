@extends('layouts.app')

@section('content')
    <div class="card shadow rounded-4 border-0">
        <div class="card-body p-4">
            <h2 class="h4 fw-bold mb-4">Buat Thread Baru</h2>

            <form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label fw-medium">Ruang Lingkup (Kategori)</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                            name="category_id" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label fw-medium">Jenis Thread</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="discussion" selected>Diskusi Umum</option>
                            <option value="job">Lowongan Pekerjaan</option>
                            <option value="alumni">Informasi Alumni</option>
                            <option value="scholarship">Beasiswa</option>
                            <option value="campus">Informasi Kampus</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label fw-medium">Tags (Opsional)</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <input type="checkbox" class="btn-check" name="tags[]" id="tag_{{ $tag->id }}"
                                value="{{ $tag->id }}" autocomplete="off">
                            <label class="btn btn-outline-secondary btn-sm rounded-pill"
                                for="tag_{{ $tag->id }}">#{{ $tag->name }}</label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-medium">Judul</label>
                    <input type="text" class="form-control form-control-lg" id="title" name="title"
                        placeholder="Apa topik diskusi hari ini?" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-medium">Isi Thread</label>
                    <textarea class="form-control" id="content" name="content" rows="6"
                        placeholder="Ceritakan lebih detail..." required></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-medium">Upload Media (Gambar/Video)</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/*,video/*">
                    <div class="form-text">Maksimal 10MB. Format: JPG, PNG, GIF, MP4, MOV, AVI.</div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="is_public" name="is_public">
                    <label class="form-check-label text-muted" for="is_public">Publik (Bisa dilihat tanpa login)</label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('home') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Posting</button>
                </div>
            </form>
        </div>
    </div>
@endsection