@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="material-icons align-middle me-2">settings</span>
                Pengaturan
            </h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                <span class="material-icons align-middle me-2">check_circle</span>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('tab', 'general') === 'general' ? 'active' : '' }}" 
                                id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                            <span class="material-icons align-middle me-1" style="font-size: 18px;">tune</span>
                            Umum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('tab') === 'seo' ? 'active' : '' }}" 
                                id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button">
                            <span class="material-icons align-middle me-1" style="font-size: 18px;">search</span>
                            SEO
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('tab') === 'forum' ? 'active' : '' }}" 
                                id="forum-tab" data-bs-toggle="tab" data-bs-target="#forum" type="button">
                            <span class="material-icons align-middle me-1" style="font-size: 18px;">forum</span>
                            Forum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ request('tab') === 'features' ? 'active' : '' }}" 
                                id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button">
                            <span class="material-icons align-middle me-1" style="font-size: 18px;">extension</span>
                            Fitur
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="tab-content" id="settingsTabsContent">
                        {{-- General Settings --}}
                        <div class="tab-pane fade {{ request('tab', 'general') === 'general' ? 'show active' : '' }}" id="general" role="tabpanel">
                            <input type="hidden" name="tab" value="general">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Situs</label>
                                    <input type="text" name="site_name" class="form-control" 
                                           value="{{ $settings['general']['site_name'] ?? 'URSpace' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tagline</label>
                                    <input type="text" name="site_tagline" class="form-control" 
                                           value="{{ $settings['general']['site_tagline'] ?? 'Forum Mahasiswa' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Deskripsi Situs</label>
                                    <textarea name="site_description" class="form-control" rows="3">{{ $settings['general']['site_description'] ?? '' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email Kontak</label>
                                    <input type="email" name="contact_email" class="form-control" 
                                           value="{{ $settings['general']['contact_email'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        {{-- SEO Settings --}}
                        <div class="tab-pane fade {{ request('tab') === 'seo' ? 'show active' : '' }}" id="seo" role="tabpanel">
                            <input type="hidden" name="tab" value="seo">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Meta Title</label>
                                    <input type="text" name="seo_meta_title" class="form-control" 
                                           value="{{ $settings['seo']['seo_meta_title'] ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Meta Description</label>
                                    <textarea name="seo_meta_description" class="form-control" rows="3">{{ $settings['seo']['seo_meta_description'] ?? '' }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Meta Keywords</label>
                                    <input type="text" name="seo_meta_keywords" class="form-control" 
                                           value="{{ $settings['seo']['seo_meta_keywords'] ?? '' }}"
                                           placeholder="keyword1, keyword2, keyword3">
                                </div>
                            </div>
                        </div>

                        {{-- Forum Settings --}}
                        <div class="tab-pane fade {{ request('tab') === 'forum' ? 'show active' : '' }}" id="forum" role="tabpanel">
                            <input type="hidden" name="tab" value="forum">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thread per Halaman</label>
                                    <input type="number" name="forum_threads_per_page" class="form-control" 
                                           value="{{ $settings['forum']['forum_threads_per_page'] ?? 15 }}" min="5" max="50">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Komentar per Halaman</label>
                                    <input type="number" name="forum_comments_per_page" class="form-control" 
                                           value="{{ $settings['forum']['forum_comments_per_page'] ?? 20 }}" min="5" max="100">
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="forum_allow_guest_view" 
                                               id="allowGuestView" value="1"
                                               {{ ($settings['forum']['forum_allow_guest_view'] ?? '1') === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allowGuestView">Izinkan tamu melihat thread</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Features Settings --}}
                        <div class="tab-pane fade {{ request('tab') === 'features' ? 'show active' : '' }}" id="features" role="tabpanel">
                            <input type="hidden" name="tab" value="features">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="feature_spaces" 
                                               id="featureSpaces" value="1"
                                               {{ ($settings['features']['feature_spaces'] ?? '1') === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featureSpaces">Aktifkan fitur Spaces (Komunitas)</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="feature_badges" 
                                               id="featureBadges" value="1"
                                               {{ ($settings['features']['feature_badges'] ?? '1') === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featureBadges">Aktifkan fitur Badges</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="feature_ai_moderation" 
                                               id="featureAI" value="1"
                                               {{ ($settings['features']['feature_ai_moderation'] ?? '0') === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featureAI">Aktifkan AI Moderation</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="feature_2fa" 
                                               id="feature2FA" value="1"
                                               {{ ($settings['features']['feature_2fa'] ?? '1') === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature2FA">Aktifkan Two-Factor Authentication</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <span class="material-icons align-middle me-1" style="font-size: 18px;">save</span>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
