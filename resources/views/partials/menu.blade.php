<div class="scrollbar" data-simplebar>
    <ul class="navbar-nav" id="navbar-nav">

        <li class="menu-title">General</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.references.index') ? 'active' : '' }}" 
               href="{{ route('admin.references.index') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:home-smile-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Tableau de Bord </span>
            </a>
        </li>

        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.formations.index') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:book-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Formations </span>
            </a>
        </li> --}}

        <li class="menu-title">Gestion</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}" 
               href="{{ route('admin.sessions.index') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:calendar-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Sessions </span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.participants.*') ? 'active' : '' }}" 
               href="{{ route('admin.participants.index') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Participants </span>
            </a>
        </li>

        <li class="menu-title">Références</li>

        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.references.index') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Tableau de Bord </span>
            </a>
        </li> --}}

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.references.create') ? 'active' : '' }}" 
               href="{{ route('admin.references.create') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:document-add-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Générer </span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.references.verify*') ? 'active' : '' }}" 
               href="{{ route('admin.references.verify.form') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:shield-check-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Vérifier </span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.references.export', 'admin.references.list', 'admin.references.download') ? 'active' : '' }}" 
               href="{{ route('admin.references.export') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:download-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Exporter </span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.legacy-aliases.*') ? 'active' : '' }}" 
               href="{{ route('admin.legacy-aliases.index') }}">
                <span class="nav-icon">
                    <iconify-icon icon="solar:link-bold-duotone"></iconify-icon>
                </span>
                <span class="nav-text"> Lier (Alias) </span>
            </a>
        </li>
    </ul>
</div>
