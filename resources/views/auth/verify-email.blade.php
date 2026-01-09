@extends('layouts.guest')

@section('content')
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h1 class="h4 fw-bold text-primary">Verifikasi Email</h1>
                        <p class="text-muted mb-0">Cek inbox email kamu untuk link verifikasi.</p>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success">
                            Link verifikasi sudah dikirim ulang.
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill">
                                Kirim Ulang Link Verifikasi
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-lg fw-bold rounded-pill">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
