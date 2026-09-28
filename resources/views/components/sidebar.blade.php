<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text my-3">Katalog Buku <sup>Dashboard</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menu
    </div>

    <!-- Nav Item - Books -->
    <li class="nav-item {{ request()->routeIs('books.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('books.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Books</span></a>
    </li>

    <!-- Nav Item - Categories -->
    @can('viewAny', App\Models\Category::class)
        <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('categories.index') }}">
                <i class="fas fa-fw fa-tags"></i>
                <span>Kategori</span></a>
        </li>
    @endcan

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
