@vite(['resources/css/header.css'])
<header>
    <div class="logo">Souciss Note</div>

    <div class="header-actions">
        @if (Route::has('login'))
            <div class="dropdown">
                @auth
                    <i class="paramètre bi bi-gear-fill" data-bs-toggle="dropdown" aria-expanded="false"></i>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Account</a></li>
                        <li><a class="dropdown-item" href="{{ route('app-settings') }}">App Settings</a></li>
                        <li>
                            <form class="dropdown-item" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
            @endif
        </div>
        @endif
        </div>
    </header>
