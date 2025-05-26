@extends('app')

@section('content')
    @vite(['resources/css/welcome.css'])
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card p-5">
                    <div class="souciss-title mb-2 text-center">Login</div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control" type="email" name="email"
                                value="{{ old('email') }}" required autofocus autocomplete="username" />
                            @if ($errors->get('email'))
                                @foreach ($errors->get('email') as $error)
                                    <div class="text-danger mt-1">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" class="form-control" type="password" name="password" required
                                autocomplete="current-password" />
                            @if ($errors->get('password'))
                                @foreach ($errors->get('password') as $error)
                                    <div class="text-danger mt-1">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label class="form-check-label" for="remember_me">Remember me</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none mb-1"
                                        style="color: #8B5CF6;">Forgot your password ?</a>
                                @endif
                                <a href="{{ route('register') }}" class="text-decoration-none" style="color: #8B5CF6;">Not
                                    yet registered ?</a>
                            </div>
                            <button type="submit" class="btn btn-violet mx-4 px-4">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
