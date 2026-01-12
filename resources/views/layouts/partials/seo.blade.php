@php
    $seoTitle = $title ?? config('app.name', 'Forum UR');
    $seoDescription = $description ?? 'Forum Diskusi Universitas Raharja - Tempat berbagi informasi dan diskusi mahasiswa.';
    $seoImage = $image ?? asset('images/og-image.jpg');
    $seoUrl = Request::url();
@endphp

{{-- Standard Meta --}}
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="Forum, Universitas Raharja, Diskusi, Mahasiswa, Teknologi, UR">
<meta name="author" content="Universitas Raharja">
<link rel="canonical" href="{{ $seoUrl }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoImage }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $seoUrl }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

{{-- Favicons --}}
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">