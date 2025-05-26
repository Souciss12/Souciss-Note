@extends('app')

@section('content')
    @vite(['resources/css/welcome.css'])
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card p-5 text-center">
                    <div class="souciss-title mb-2">SoucissNote</div>
                    <div class="mb-4 text-secondary">A modern and user-friendly note-taking application with MarkDown
                    </div>
                    @if (Route::has('login'))
                        <div class="d-flex justify-content-center gap-3">
                            @auth
                                <a href="{{ route('note.index') }}" class="btn btn-violet px-4">Mes notes</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-violet px-4">Login</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-violet px-4">Register</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
