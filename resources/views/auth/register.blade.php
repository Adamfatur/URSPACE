@extends('layouts.guest')

@section('content')
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-primary">Daftar</h1>
                        <p class="text-muted">Bergabung dengan Komunitas Raharja</p>
                    </div>

                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-medium">Username</label>
                            <input type="text" class="form-control form-control-lg @error('username') is-invalid @enderror"
                                id="username" name="username" value="{{ old('username') }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Raharja (@raharja.info /
                                @raharja.ac.id)</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" placeholder="nama@raharja.info" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <input type="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror" id="password"
                                name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Password</label>
                            <input type="password" class="form-control form-control-lg" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill">Daftar</button>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">Sudah punya akun? <a href="{{ route('login') }}"
                                    class="text-primary text-decoration-none fw-bold">Masuk</a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection