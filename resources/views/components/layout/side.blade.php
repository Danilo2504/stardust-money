<div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="sidebar">
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Gastos</span>
        </a>

        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-folder"></i>
            <span>Categorías</span>
        </a>

        <a href="{{ route('recurring-expenses.index') }}" class="sidebar-link {{ request()->routeIs('recurring-expenses.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-repeat"></i>
            <span>Recurrentes</span>
        </a>

        <a href="{{ route('installment-groups.index') }}" class="sidebar-link {{ request()->routeIs('installment-groups.*') ? 'active' : '' }}">
            <i class="bi bi-layers"></i>
            <span>Cuotas</span>
        </a>

        <a href="{{ route('shared-reports.index') }}" class="sidebar-link {{ request()->routeIs('shared-reports.*') ? 'active' : '' }}">
            <i class="bi bi-link-45deg"></i>
            <span>Reportes</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        {{ config('app.name', 'Stardust Money') }} &copy; {{ date('Y') }}
    </div>
</aside>
