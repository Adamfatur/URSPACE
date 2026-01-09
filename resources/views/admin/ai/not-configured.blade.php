@extends('layouts.admin')

@section('title', 'AI Not Configured')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-5 text-center">
                        <span class="material-icons text-warning mb-3" style="font-size: 64px;">warning</span>
                        <h4 class="fw-bold mb-3">Gemini AI Belum Dikonfigurasi</h4>
                        <p class="text-muted mb-4">
                            Untuk menggunakan fitur AI, Anda perlu menambahkan API key Gemini di file <code>.env</code>
                        </p>

                        <div class="bg-light rounded-4 p-4 text-start mb-4">
                            <p class="fw-bold mb-2">Tambahkan baris berikut ke file .env:</p>
                            <code class="d-block p-3 bg-dark text-success rounded-3">
                                GEMINI_API_KEY=your_api_key_here<br>
                                GEMINI_MODEL=gemini-2.0-flash
                            </code>
                        </div>

                        <p class="text-muted small mb-4">
                            Dapatkan API key gratis di
                            <a href="https://aistudio.google.com/apikey" target="_blank" class="text-primary">Google AI
                                Studio</a>
                        </p>

                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary rounded-pill px-4">
                            <span class="material-icons align-middle me-1" style="font-size: 18px;">arrow_back</span>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection