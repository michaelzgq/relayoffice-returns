@php
    $metaSiteName = $metaSiteName
        ?? ($appName ?? (config('app.name') === 'Laravel' ? 'Dossentry' : config('app.name', 'Dossentry')));
    $metaTitle = $metaTitle ?? $metaSiteName;
    $metaDescription = $metaDescription ?? '';
    $metaUrl = $metaUrl ?? url()->current();
    $metaImage = $metaImage ?? asset('assets/dossentry/og-home.png');
    $metaImageAlt = $metaImageAlt ?? $metaTitle;
    $metaType = $metaType ?? 'website';
@endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $metaUrl }}">
    <meta property="og:type" content="{{ $metaType }}">
    <meta property="og:site_name" content="{{ $metaSiteName }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $metaImageAlt }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
