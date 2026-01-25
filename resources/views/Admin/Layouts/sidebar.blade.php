<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/wdms/pit-logo-outlined.png') }}"
                     alt="Pit Logo"
                     class="w-px-40 h-auto" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">WDMS</span>
        </a>

        <a href="javascript:void(0);"
           class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- ================= ADMIN ================= --}}
        @if (auth()->user()->user_type === 'ADMIN')

            <li class="menu-item {{ Route::is('users.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div>Users</div>
                    <span class="badge bg-warning rounded-pill ms-auto">!</span>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('users.index') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="menu-link">
                            <div>Verify Users</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Route::is('users.taskforce.index') ? 'active' : '' }}">
                        <a href="{{ route('users.taskforce.index') }}" class="menu-link">
                            <div>Task Force</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ Route::is('admin.accreditation.*') ? 'active' : '' }}">
                <a href="{{ route('admin.accreditation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>Admin Accreditation</div>
                </a>
            </li>
               <li class="menu-item {{ Route::is('archive.*') ? 'active' : '' }}">
            <a href="{{ route('archive.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-folder"></i>
                <div>Archive</div>
            </a>
    </li>
        {{-- ================= TASK FORCE (ACTIVE) ================= --}}
        @elseif (auth()->user()->user_type === 'TASK FORCE' && auth()->user()->status === 'Active')

            <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div>TASKFORCE DASHBOARD</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('admin.accreditation.*') ? 'active' : '' }}">
                <a href="{{ route('admin.accreditation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>TASKFORCE Accreditation</div>
                </a>
            </li>

        {{-- ================= TASK FORCE (NOT ACTIVE) ================= --}}
        @elseif (auth()->user()->user_type === 'TASK FORCE')

            <li class="menu-item disabled">
                <span class="menu-link text-muted">
                    <i class="menu-icon tf-icons bx bx-lock"></i>

                    @if (auth()->user()->status === 'Pending')
                        <div>Account Under Review</div>
                    @elseif (auth()->user()->status === 'Inactive')
                        <div>Account Inactive</div>
                    @elseif (auth()->user()->status === 'Suspended')
                        <div>Account Suspended</div>
                    @else
                        <div>Account Not Active</div>
                    @endif
                </span>
            </li>

        {{-- ================= INTERNAL ACCESSOR ================= --}}
        @elseif (auth()->user()->user_type === 'INTERNAL ACCESSOR')

            <li class="menu-item {{ Route::is('internal-accessor.*') ? 'active' : '' }}">
                <a href="{{ route('internal-accessor.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>Internal Accessor Accreditation</div>
                </a>
            </li>

        {{-- ================= ACCREDITOR ================= --}}
        @elseif (auth()->user()->user_type === 'ACCREDITOR')

            <li class="menu-item {{ Route::is('internal-accessor.*') ? 'active' : '' }}">
                <a href="{{ route('internal-accessor.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>ACCREDITOR Accreditation</div>
                </a>
            </li>
             <li class="menu-item {{ Route::is('archive.*') ? 'active' : '' }}">
            <a href="{{ route('archive.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-folder"></i>
                <div>Archive</div>
            </a>
    </li>

        @endif

    </ul>
</aside>
