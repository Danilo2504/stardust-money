<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ms-auto">

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name ?? 'Usuario' }}</span>
                <span class="img-profile rounded-circle d-inline-flex align-items-center justify-content-center bg-gradient-primary text-white font-weight-bold">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                    Perfil
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </li>

    </ul>

</nav>
