<div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>
<div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h5 class="m-0">Menu</h5>
        <button class="btn btn-sm btn-light sidebar-close" onclick="toggleSidebar()">&times;</button>
    </div>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        Dashboard
    </a>
    <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
        Gastos
    </a>
    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
        Categorias
    </a>
</div>