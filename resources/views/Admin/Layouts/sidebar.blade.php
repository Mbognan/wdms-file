@php
    use App\Enums\UserType;
    use App\Enums\UserStatus;

    $user = auth()->user();
    $currentRole = $user->currentRole->name;
    $isActive = $user->status === UserStatus::ACTIVE->value;

    // Define sidebar color class based on user type
    $sidebarClass = match ($user->currentRole->name) {
        UserType::ADMIN->value => 'sidebar-admin',
        UserType::DEAN->value => 'sidebar-dean',
        UserType::TASK_FORCE->value => 'sidebar-taskforce',
        UserType::INTERNAL_ASSESSOR->value => 'sidebar-internal',
        UserType::ACCREDITOR->value => 'sidebar-accreditor',
        default => 'sidebar-default',
    };
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme {{ $sidebarClass }}">
    <div class="app-brand demo">
        <a href={{ route('dashboard') }} class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/wdms/pit-logo-outlined.png') }}"
                     alt="Pit Logo"
                     class="w-px-50 h-auto" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2 text-uppercase">WADMS</span>
        </a>

        <a href="javascript:void(0);"
           class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- ================= ADMIN ================= --}}
        @if ($isActive && $currentRole === UserType::ADMIN->value)
            <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('users.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div>Internal Assessors & Accreditors</div>
                    @if ($unverifiedCount > 0 || $pendingRoleRequestCount > 0)
                        <span class="badge bg-warning rounded-pill ms-auto">
                            !
                        </span>
                    @endif
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('users.index') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="menu-link">
                            <div>Pending Accounts</div>
                            @if ($unverifiedCount > 0)
                                <span class="badge bg-warning rounded-pill ms-auto">
                                    {{ $unverifiedCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <li class="menu-item {{ Route::is('users.taskforce.index') ? 'active' : '' }}">
                        <a href="{{ route('users.taskforce.index') }}" class="menu-link">
                            <div>Active Accounts</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Route::is('role-requests.*') ? 'active' : '' }}">
                        <a href="{{ route('role-requests.index') }}" class="menu-link">
                            <div>Role Requests</div>
                            @if ($pendingRoleRequestCount > 0)
                                <span class="badge bg-warning rounded-pill ms-auto">
                                    {{ $pendingRoleRequestCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ Route::is('admin.accreditation.*') ? 'active' : '' }}">
                <a href="{{ route('admin.accreditation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>Accreditation</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('program.areas.*') ? 'active' : '' }}">
                <a href="{{ route('program.areas.evaluations') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-clipboard"></i>
                    <div>Evaluations</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('archive.*') ? 'active' : '' }}">
                <a href="{{ route('archive.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-folder"></i>
                    <div>Archive</div>
                </a>
            </li>

        {{-- ================= DEAN ================= --}}
        @elseif ($isActive && $currentRole === UserType::DEAN->value)

            <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('users.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div>Task Forces</div>
                    @if ($unverifiedCount > 0 || $pendingRoleRequestCount > 0)
                        <span class="badge bg-warning rounded-pill ms-auto">
                            !
                        </span>
                    @endif
                </a>

                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('users.index') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="menu-link">
                            <div>Unverified</div>
                            @if ($unverifiedCount > 0)
                                <span class="badge bg-warning rounded-pill ms-auto">
                                    {{ $unverifiedCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <li class="menu-item {{ Route::is('users.taskforce.index') ? 'active' : '' }}">
                        <a href="{{ route('users.taskforce.index') }}" class="menu-link">
                            <div>Verified</div>
                        </a>
                    </li>

                    <li class="menu-item {{ Route::is('role-requests.*') ? 'active' : '' }}">
                        <a href="{{ route('role-requests.index') }}" class="menu-link">
                            <div>Role Requests</div>
                            @if ($pendingRoleRequestCount > 0)
                                <span class="badge bg-warning rounded-pill ms-auto">
                                    {{ $pendingRoleRequestCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ Route::is('admin.accreditation.*') ? 'active' : '' }}">
                <a href="{{ route('admin.accreditation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>Accreditation</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('program.areas.*') ? 'active' : '' }}">
                <a href="{{ route('program.areas.evaluations') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-clipboard"></i>
                    <div>Evaluations</div>
                </a>
            </li>

        {{-- ================= TASK FORCE ================= --}}
        @elseif ($isActive && $currentRole === UserType::TASK_FORCE->value)

            <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('admin.accreditation.*') ? 'active' : '' }}">
                <a href="{{ route('admin.accreditation.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>Accreditation</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('program.areas.*') ? 'active' : '' }}">
                <a href="{{ route('program.areas.evaluations') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-clipboard"></i>
                    <div>Evaluations</div>
                </a>
            </li>

        {{-- ================= NOT ACTIVE ================= --}}
        @elseif (!$isActive)

            <li class="menu-item disabled">
                <span class="menu-link text-muted">
                    <i class="menu-icon tf-icons bx bx-lock"></i>
                    <div>
                        @switch($user->status)
                            @case('Pending')
                                Account Under Review
                                @break

                            @case('Inactive')
                                Account Inactive
                                @break

                            @case('Suspended')
                                Account Suspended
                                @break

                            @default
                                Account Not Active
                        @endswitch
                    </div>
                </span>
            </li>

        {{-- ================= INTERNAL ASSESSOR ================= --}}
        @elseif ($isActive && $currentRole === UserType::INTERNAL_ASSESSOR->value)

            <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('internal-accessor.*') ? 'active' : '' }}">
                <a href="{{ route('internal-accessor.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>Accreditation</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('program.areas.*') ? 'active' : '' }}">
                <a href="{{ route('program.areas.evaluations') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-clipboard"></i>
                    <div>Evaluations</div>
                </a>
            </li>

        {{-- ================= ACCREDITOR ================= --}}
        @elseif ($isActive && $currentRole === UserType::ACCREDITOR->value)

            <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div>Dashboard</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('internal-accessor.*') ? 'active' : '' }}">
                <a href="{{ route('internal-accessor.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-badge-check"></i>
                    <div>ACCREDITOR Accreditation</div>
                </a>
            </li>

            <li class="menu-item {{ Route::is('program.areas.*', 'evaluations.*') ? 'active' : '' }}">
                <a href="{{ route('program.areas.evaluations') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-folder"></i>
                    <div>Evaluations</div>
                </a>
            </li>
        @endif
    </ul>
</aside>
