<!doctype html>
<html lang="fr">

<head>
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <title>@yield('title')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials/styles')
    @vite(['resources/css/app.css'])
</head>

<body
    style="
    --primary-color: {{ $colors['primary_color'] ?? '#8B5CF6' }};
    --secondary-color: {{ $colors['secondary_color'] ?? '#A78BFA' }};
    --hover-color: {{ $colors['hover_color'] ?? '#DDD6FE' }};
    --background1-color: {{ $colors['background1_color'] ?? '#F5F3FF' }};
    --background2-color: {{ $colors['background2_color'] ?? '#FFFFFF' }};
    --black-text-color: {{ $colors['black_text_color'] ?? '#1F2937' }};
    --white-text-color: {{ $colors['white_text_color'] ?? '#F5F3FF' }}; background-color: var(--background2-color);
">
    @include('partials/header')
    @yield('content')
    @include('partials/script')
</body>

</html>
