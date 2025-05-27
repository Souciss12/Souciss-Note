<section>
    <h4 class="mb-3 edit-title">Profile Information</h4>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" class="form-control"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if ($errors->get('name'))
                @foreach ($errors->get('name') as $error)
                    <div class="text-danger mt-1">{{ $error }}</div>
                @endforeach
            @endif
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control"
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @if ($errors->get('email'))
                @foreach ($errors->get('email') as $error)
                    <div class="text-danger mt-1">{{ $error }}</div>
                @endforeach
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-warning small">
                        Your email address is unverified.
                        <button form="send-verification" class="btn btn-link p-0 text-decoration-underline small"
                            style="color: #8B5CF6;">
                            Click here to re-send the verification email.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success small mt-2">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-violet px-4">Save Changes</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small ms-3 align-self-center">Saved!</span>
            @endif
        </div>
    </form>
</section>
<style>
    .edit-title {
        color: var(--secondary-color);
    }
</style>
