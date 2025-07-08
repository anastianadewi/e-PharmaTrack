<div id="sidebar" class="text-white sidebar">
    <ul class="nav flex-column mt-3 px-1">

        <!-- Dashboard -->
        <li>
            <a href="{{ route('dashboard') }}" class="nav-item d-flex align-items-center {{ request()->routeIs('dashboard') ? 'bg-navbar-light' : '' }}">
                <i class="bi bi-house sidebar-icon"></i>
                <span class="ms-2 sidebar-text">Dashboard</span>
            </a>
        </li>

        @if(in_array(auth()->user()->role, ['nakes']))
        <!-- Jenis Obat -->
        <li>
            <a href="{{ route('jenisobat.index') }}" class="nav-item d-flex align-items-center {{ request()->routeIs('jenisobat.index') ? 'bg-navbar-light' : '' }}">
                <i class="bi bi-ui-checks-grid sidebar-icon"></i>
                <span class="ms-2 sidebar-text">Jenis Obat</span>
            </a>
        </li>
        @endif

        <!-- Stok Obat -->
        <li>
            <a href="{{ route('stokobat.index') }}" class="nav-item d-flex align-items-center {{ request()->routeIs('stokobat.index') ? 'bg-navbar-light' : '' }}">
                <i class="bi bi-capsule sidebar-icon"></i>
                <span class="ms-2 sidebar-text">Stok Obat</span>
            </a>
        </li>

        <!-- Obat Masuk -->
        <li>
            <a href="{{ route('obatmasuk.index') }}" class="nav-item d-flex align-items-center {{ request()->routeIs('obatmasuk.index') ? 'bg-navbar-light' : '' }}">
                <i class="bi bi-box-arrow-in-down sidebar-icon"></i>
                <span class="ms-2 sidebar-text">Obat Masuk</span>
            </a>
        </li>

        <!-- Obat Keluar -->
        <li>
            <a href="{{ route('obatkeluar.index') }}" class="nav-item d-flex align-items-center {{ request()->routeIs('obatkeluar.index') || request()->routeIs('obatkeluar.show') ? 'bg-navbar-light' : '' }}">
                <i class="bi bi-box-arrow-up sidebar-icon"></i>
                <span class="ms-2 sidebar-text">Obat Keluar</span>
            </a>
        </li>

        <!-- Obat Terhapus -->
        <li>
            <a href="{{ route('obatterhapus.index') }}" class="nav-item d-flex align-items-center {{ request()->routeIs('obatterhapus.index') || request()->routeIs('obatterhapus.show') ? 'bg-navbar-light' : '' }}">
                <i class="bi bi-archive sidebar-icon"></i>
                <span class="ms-2 sidebar-text">Obat Terhapus</span>
            </a>
        </li>

        @if(in_array(auth()->user()->role, ['nakes']))
        <!-- User -->
        <li>
            <a href="{{ route('user') }}" class="nav-item d-flex align-items-center {{ request()->routeIs('user') ? 'bg-navbar-light' : '' }}">
                <i class="bi bi-person sidebar-icon"></i>
                <span class="ms-2 sidebar-text">User</span>
            </a>
        </li>
        @endif

    </ul>
</div>
