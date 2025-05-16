<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoucissNote</title>
    @include('partials/styles')
    @vite(['resources/css/welcome.css'])
</head>

<body>
    <div class="card p-5 text-center">
        <div class="souciss-title mb-2">SoucissNote</div>
        <div class="mb-4 text-secondary">L'application simple et moderne pour organiser toutes vos notes en Markdown.
        </div>
        @if (Route::has('login'))
            <div class="d-flex justify-content-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-violet px-4">Mon tableau de bord</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-violet px-4">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-violet px-4">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
    @include('partials/script')
</body>

</html>
