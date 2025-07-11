<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <!-- Character Encoding -->
    <meta charset="UTF-8">

    <!-- Viewport for Responsive Design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title (Use in <title> tag instead of meta) -->
    <title>Fake News Detector - Check if a news article is real or fake</title>

    <!-- Description (160–180 characters ideal) -->
    <meta name="description" content="Fake News Detector - Check if a news article is real or fake">

    <!-- Keywords (not used by Google, optional) -->
    <meta name="keywords" content="fake news detector, check if news article is fake, check if news article is real">

    <!-- Author -->
    <meta name="author" content="Australian Clearing">

    <!-- Robots -->
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="Fake News Detector - Check if a news article is real or fake">
    <meta property="og:description" content="Check if a news article is real or fake">
    <meta property="og:image" content="https://fakenewsdetector.co/logo.jpg">
    <meta property="og:url" content="https://fakenewsdetector.co">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Fake News Detector">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Fake News Detector">
    <meta name="twitter:description" content="Check if a news article is real or fake">
    <meta name="twitter:image" content="https://fakenewsdetector.co/logo.jpg">


    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    @endif
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @turnstileScripts()
    <!-- Other head elements -->
</head>

<body class="bg-gradient-to-br bg-blue-100 min-h-screen">

    @yield('content')

</body>

</html>
