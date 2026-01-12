@extends('layouts.guest')

@section('content')
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-primary">Verifikasi 2FA</h1>
                        <p class="text-muted">Masukkan kode otp yang dikirim ke email anda</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('2fa.verify.post') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="code" class="form-label fw-medium text-center d-block">Kode Verifikasi (6
                                Digit)</label>
                            <input type="text" class="form-control form-control-lg text-center fs-2 letter-spacing-2"
                                id="code" name="code" maxlength="6" autofocus required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill">Verifikasi</button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-muted text-decoration-none">Kembali ke Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .letter-spacing-2 {
            letter-spacing: 0.5em;
        }
    </style>
@endsection