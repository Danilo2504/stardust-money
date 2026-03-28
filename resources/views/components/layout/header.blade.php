<header>
    <nav class="app-header d-flex align-items-center justify-content-between px-3 fixed-top">
        <div>
        <button class="btn btn-light" onclick="toggleSidebar()">☰</button>
        </div>

        <div>
        <strong>Mi Dashboard</strong>
        </div>

        <div class="dropdown">
        <button class="btn btn-light dropdown-toggle user-menu" data-bs-toggle="dropdown">
            Usuario
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><a class="dropdown-item" href="#">Preferencias</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#">Cerrar sesión</a></li>
        </ul>
        </div>
    </nav>
</header>