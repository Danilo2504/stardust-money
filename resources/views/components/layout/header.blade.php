<header class="app-header">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light btn-sm btn-menu" onclick="toggleSidebar()" aria-label="Abrir menú">
            <i class="bi bi-list fs-5"></i>
        </button>

        <a href="{{ route('dashboard') }}" class="app-brand">
            <span class="app-brand-icon">
                <i class="bi bi-wallet2"></i>
            </span>
            <span>{{ config('app.name', 'Stardust Money') }}</span>
        </a>
    </div>

    <div class="dropdown">
        <button class="user-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
            <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Usuario' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" style="border-radius: 0.75rem;">
            <li>
                <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person me-2"></i> Perfil
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item py-2 text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
