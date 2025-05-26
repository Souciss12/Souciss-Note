<section>
    <h4 class="mb-3 edit-title">Update Password</h4>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Current Password</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control"
                autocomplete="current-password" />
            @if ($errors->updatePassword->get('current_password'))
                @foreach ($errors->updatePassword->get('current_password') as $error)
                    <div class="text-danger mt-1">{{ $error }}</div>
                @endforeach
            @endif
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="update_password_password" class="form-label">New Password</label>
            <input id="update_password_password" name="password" type="password" class="form-control"
                autocomplete="new-password" />
            @if ($errors->updatePassword->get('password'))
                @foreach ($errors->updatePassword->get('password') as $error)
                    <div class="text-danger mt-1">{{ $error }}</div>
                @endforeach
            @endif
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control" autocomplete="new-password" />
            @if ($errors->updatePassword->get('password_confirmation'))
                @foreach ($errors->updatePassword->get('password_confirmation') as $error)
                    <div class="text-danger mt-1">{{ $error }}</div>
                @endforeach
            @endif
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-violet px-4">Update Password</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small ms-3 align-self-center">Password updated!</span>
            @endif
        </div>
    </form>
</section>
<style>
    .edit-title {
        color: #8B5CF6
    }
</style>
