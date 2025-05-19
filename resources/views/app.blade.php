<!doctype html>
<html lang="fr">

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    @include('partials/styles')
</head>

<body>
    @include('partials/header')
    <main>
        @yield('content')
    </main>
    @include('partials/script')
</body>

</html>
