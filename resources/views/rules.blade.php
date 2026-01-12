@extends('layouts.app')

@section('title', 'Aturan Forum - Forum UR')
@section('meta_description', 'Aturan dan pedoman komunitas Forum UR untuk menjaga diskusi yang sehat dan produktif di lingkungan Universitas Raharja.')
@section('canonical', route('rules'))

@section('content')
    <div class="card shadow rounded-4 border-0">
        <div class="card-body p-4">
            <div class="text-center mb-5">
                <span class="material-icons text-primary" style="font-size: 64px;">gavel</span>
                <h1 class="h3 fw-bold mt-2">Aturan Forum UR</h1>
                <p class="text-muted">Harap baca dan patuhi aturan di bawah ini untuk menjaga komunitas tetap sehat.</p>
            </div>

            <div class="d-flex flex-column gap-4">
                @foreach($rules as $rule)
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center fw-bold"
                                style="width: 32px; height: 32px;">
                                {{ $loop->iteration }}
                            </div>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $rule->title }}</h5>
                            <p class="text-muted mb-0">{{ $rule->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 p-3 rounded-4 bg-light text-center border">
                <p class="small text-muted mb-0">
                    Pelanggaran terhadap aturan di atas dapat berakibat pada penghapusan konten hingga pemblokiran akun
                    secara permanen.
                </p>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-5 fw-bold">Saya Mengerti</a>
            </div>
        </div>
    </div>
@endsection