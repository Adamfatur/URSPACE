@extends('layouts.guest')

@section('content')
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-primary">Masuk</h1>
                        <p class="text-muted">Forum Universitas Raharja</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(config('services.google.client_id') && config('services.google.client_secret') && config('services.google.redirect'))
                        <div class="d-grid mb-4">
                            <a href="{{ route('auth.google.redirect') }}" class="btn btn-primary btn-lg fw-bold rounded-pill">
                                <i class="material-icons align-middle me-2">alternate_email</i>
                                Login with Rinfo
                            </a>
                        </div>
                        <div class="text-center text-muted small mb-3">atau masuk dengan akun manual</div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Raharja</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="nama@raharja.info" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i
                                        class="material-icons">visibility</i></button>
                            </div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-outline-primary btn-lg fw-bold rounded-pill">Masuk Manual</button>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">Belum punya akun? <a href="{{ route('register') }}"
                                    class="text-primary text-decoration-none fw-bold">Daftar Sekarang</a></small>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    </script>
@endsection