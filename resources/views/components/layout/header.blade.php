<header>
    <nav class="app-header d-flex align-items-center justify-content-between px-3 fixed-top">
        <div>
        <button class="btn btn-light" onclick="toggleSidebar()">☰</button>
        </div>

        <div>
        <strong>{{ config('app.name', 'Stardust Money') }}</strong>
        </div>

        <div class="dropdown">
        <button class="btn btn-light dropdown-toggle user-menu" data-bs-toggle="dropdown">
            {{ Auth::user()->name ?? 'Usuario' }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">Cerrar sesión</button>
                </form>
            </li>
        </ul>
        </div>
    </nav>
</header>