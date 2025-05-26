@extends('app')

@section('content')
    @vite(['resources/css/welcome.css'])
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card p-5">
                    <div class="souciss-title mb-2 text-center">Register</div>


                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input id="name" class="form-control" type="text" name="name"
                                value="{{ old('name') }}" required autofocus autocomplete="name" />
                            @if ($errors->get('name'))
                                @foreach ($errors->get('name') as $error)
                                    <div class="text-danger mt-1">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control" type="email" name="email"
                                value="{{ old('email') }}" required autocomplete="username" />
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
                                autocomplete="new-password" />
                            @if ($errors->get('password'))
                                @foreach ($errors->get('password') as $error)
                                    <div class="text-danger mt-1">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm password</label>
                            <input id="password_confirmation" class="form-control" type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                            @if ($errors->get('password_confirmation'))
                                @foreach ($errors->get('password_confirmation') as $error)
                                    <div class="text-danger mt-1">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('login') }}" class="text-decoration-none mx-4" style="color: #8B5CF6;">Already
                                registered
                                ?</a>
                            <button type="submit" class="btn btn-violet mx-4 px-4">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
