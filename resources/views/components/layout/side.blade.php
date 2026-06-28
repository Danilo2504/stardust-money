<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('app.name', 'Stardust') }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Gastos
    </div>

    <!-- Nav Item - Expenses -->
    <li class="nav-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('expenses.index') }}">
            <i class="fas fa-fw fa-receipt"></i>
            <span>Gastos</span>
        </a>
    </li>

    <!-- Nav Item - Categories -->
    <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('categories.index') }}">
            <i class="fas fa-fw fa-folder"></i>
            <span>Categorías</span>
        </a>
    </li>

    <!-- Nav Item - Recurring Expenses -->
    <li class="nav-item {{ request()->routeIs('recurring-expenses.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('recurring-expenses.index') }}">
            <i class="fas fa-fw fa-sync-alt"></i>
            <span>Recurrentes</span>
        </a>
    </li>

    <!-- Nav Item - Installments -->
    <li class="nav-item {{ request()->routeIs('installment-groups.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('installment-groups.index') }}">
            <i class="fas fa-fw fa-layer-group"></i>
            <span>Cuotas</span>
        </a>
    </li>

    <!-- Nav Item - Shared Reports -->
    <li class="nav-item {{ request()->routeIs('shared-reports.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('shared-reports.index') }}">
            <i class="fas fa-fw fa-link"></i>
            <span>Reportes</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-block">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
