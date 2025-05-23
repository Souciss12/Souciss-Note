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

<body>
    @include('partials/header')
    @yield('content')
    @include('partials/script')
</body>

</html>
