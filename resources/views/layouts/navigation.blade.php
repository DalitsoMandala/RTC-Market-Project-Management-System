<div class="d-none">
    @include('layouts.other-navs')
</div>

<style>
    .custom-container {
        max-width: 92%;
    }

    #page-topbar {
        margin-bottom: 1.5rem;
    }

    /* ROW 1 — brand + dashboard switch buttons */
    #topbar-row1 .btn-outline-warning.active {
        background-color: #fc931d;
        color: #fff;
        border-color: #fc931d;
    }

    /* ROW 2 — main nav */
    .navbar-bottom .nav-link {
        color: #495057;
        font-weight: 500;
        display: flex;
        align-items: center;
        line-height: 1;
    }

    .navbar-bottom .nav-link i {
        font-size: 1.05rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
    }

    .navbar-bottom .dropdown-item {
        display: flex;
        align-items: center;
    }

    .navbar-bottom .nav-link:hover,
    .navbar-bottom .nav-link.active {
        color: #f1a53a;
    }

    .navbar-bottom .header-profile-user {
        height: 36px;
        width: 36px;
        object-fit: cover;
    }

    /* Nested (multi-level) dropdowns */
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu>.dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: -.25rem;
    }

    @media (min-width: 992px) {
        .dropdown-submenu:hover>.dropdown-menu {
            display: block;
        }
    }

    @media (max-width: 991.98px) {

        .dropdown-submenu>.dropdown-menu {
            display: none;
            position: static;
            margin-left: 1rem;
            border: none;
            box-shadow: none;
        }

        .dropdown-submenu.show>.dropdown-menu {
            display: block;
        }
    }
</style>

<header id="page-topbar" class="border-top">

    {{-- ============ ROW 1: brand + dashboard switch buttons ============ --}}
    <div class="py-2 bg-white border-bottom" id="topbar-row1">
        <div
            class="gap-2 container-fluid custom-container d-flex flex-column flex-lg-row justify-content-between align-items-center">

            <a class="mb-0 text-black navbar-brand d-flex align-items-center" href="/">
                <x-application-logo width="42" />
                <span class="ms-2 fw-semibold">{{ config('app.name') }}</span>
            </a>

            @php
                $routePrefix = trim(\Illuminate\Support\Facades\Route::current()->getPrefix(), '/');
            @endphp

            @hasanyrole('admin|manager|project_manager|staff|monitor|enumerator')
                <ul class="flex-wrap gap-2 mb-0 nav align-items-center justify-content-center">
                    <li class="nav-item">
                        <a href="{{ url('/') }}" id="dashboard-one"
                            class="btn btn-sm px-3 btn-outline-warning {{ request()->is($routePrefix . '/dashboard') ? 'active' : '' }}">
                            Project Report
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url($routePrefix . '/dashboard-2') }}" id="dashboard-two"
                            class="btn btn-sm px-3 btn-outline-warning {{ request()->is($routePrefix . '/dashboard-2') ? 'active' : '' }}">
                            Market Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url($routePrefix . '/dashboard-3') }}" id="dashboard-three"
                            class="btn btn-sm px-3 btn-outline-warning {{ request()->is($routePrefix . '/dashboard-3') ? 'active' : '' }}">
                            Gross Margins
                        </a>
                    </li>
                </ul>
            @endhasanyrole
        </div>
    </div>

    {{-- ============ ROW 2: main navigation ============ --}}
    <nav class="bg-white navbar navbar-expand-lg navbar-light navbar-bottom border-bottom" x-data>
        <div class="flex-wrap container-fluid custom-container d-flex align-items-center">

            <button class="order-1 navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#topnav-menu-content" aria-controls="topnav-menu-content" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="order-3 collapse navbar-collapse order-lg-2 flex-grow-1" id="topnav-menu-content">

                @hasallroles('admin')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin-dashboard') ? 'active' : '' }}"
                                href="{{ route('admin-dashboard') }}">
                                <i class='bx bx-home me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin-users') }}">
                                <i class='bx bx-user me-1'></i> Manage Users
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class='bx bx-folder me-1'></i> Data Management
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Project Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin-period') }}">Reporting
                                                Periods</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Indicator Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin-indicators') }}">Indicators</a>
                                        </li>
                                        <li><a class="dropdown-item" href="/admin/baseline">Manage Baseline Data</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin-std-targets') }}">Indicator
                                                Targets</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin-targets') }}">View Targets</a>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('admin-sources') }}">Indicator
                                                Sources</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Operations Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin-forms') }}">Form Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin-submissions') }}">Submissions</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin-submission-period') }}">Submission Periods</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin-reports') }}">Reports</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin-enterprise-management') }}">Enterprise
                                                Management</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin-aggregated-reports') }}">Aggregated Reports</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Marketing Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin-markets-manage-data') }}">Manage
                                                Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin-markets-submit-data') }}">Marketing Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Gross Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin-gross-margin-manage-data') }}">Manage Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin-gross-margin-add-data') }}">Gross Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin-setup') }}">
                                <i class='bx bx-cog me-1'></i> Settings
                            </a>
                        </li>
                    </ul>
                @endhasallroles

                @hasallroles('monitor')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('monitor-dashboard') }}">
                                <i class='bx bx-home me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('monitor-users') }}">
                                <i class='bx bx-user me-1'></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class='bx bx-folder me-1'></i> Data Management
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Project Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('monitor-period') }}">Reporting
                                                Periods</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Indicator Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('monitor-indicators') }}">Indicators</a></li>
                                        <li><a class="dropdown-item" href="/monitor/baseline">Manage Baseline Data</a>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('monitor-std-targets') }}">Indicator
                                                Targets</a></li>
                                        <li><a class="dropdown-item" href="{{ route('monitor-targets') }}">View
                                                Targets</a></li>
                                        <li><a class="dropdown-item" href="{{ route('monitor-sources') }}">Indicator
                                                Sources</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Operations Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('monitor-forms') }}">Form Data</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('monitor-submissions') }}">Submissions</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('monitor-submission-period') }}">Submission Periods</a>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('monitor-reports') }}">Reports</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Marketing Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('monitor-markets-manage-data') }}">Manage Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('monitor-markets-submit-data') }}">Marketing Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Gross Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('monitor-gross-margin-manage-data') }}">Manage Data</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('monitor-gross-margin-add-data') }}">Gross Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('monitor-setup') }}">
                                <i class='bx bx-cog me-1'></i> Settings
                            </a>
                        </li>
                    </ul>
                @endhasallroles

                @hasallroles('manager')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cip-dashboard') }}">
                                <i class='bx bx-home me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class='bx bx-folder me-1'></i> Data Management
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('cip-baseline') }}">Baseline Data</a></li>
                                <li><a class="dropdown-item" href="{{ route('cip-indicators') }}">Indicators</a></li>
                                <li><a class="dropdown-item" href="{{ route('cip-forms') }}">Form Data</a></li>
                                <li><a class="dropdown-item" href="{{ route('cip-submission-period') }}">Submission
                                        Periods</a></li>
                                <li><a class="dropdown-item" href="{{ route('cip-targets') }}">View Targets</a></li>
                                <li><a class="dropdown-item" href="{{ route('cip-submissions') }}">Submissions</a></li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Marketing Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('cip-markets-manage-data') }}">Manage
                                                Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('cip-markets-submit-data') }}">Marketing Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Gross Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('cip-gross-margin-manage-data') }}">Manage Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('cip-gross-margin-add-data') }}">Gross Data Submission</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cip-reports') }}">
                                <i class='bx bx-table me-1'></i> Reports
                            </a>
                        </li>
                    </ul>
                @endhasallroles

                @hasallroles('external')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('external-dashboard') }}">
                                <i class='bx bx-tachometer me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class='bx bx-folder me-1'></i> Data Management
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('external-indicators') }}">Indicators</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('external-forms') }}">Form Data</a></li>
                                <li><a class="dropdown-item" href="{{ route('external-targets') }}">View Targets</a></li>
                                <li><a class="dropdown-item" href="{{ route('external-submission-period') }}">Submission
                                        Periods</a></li>
                                <li><a class="dropdown-item" href="{{ route('external-submissions') }}">Submissions</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('external-reports') }}">
                                <i class='bx bx-table me-1'></i> Reports
                            </a>
                        </li>
                    </ul>
                @endhasallroles

                @hasallroles('staff')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cip-staff-dashboard') }}">
                                <i class='bx bx-tachometer me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class='bx bx-folder me-1'></i> Data Management
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('cip-staff-indicators') }}">Indicators</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('cip-staff-forms') }}">Form Data</a></li>
                                <li><a class="dropdown-item" href="{{ route('cip-staff-targets') }}">View Targets</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('cip-staff-submission-period') }}">Submission
                                        Periods</a></li>
                                <li><a class="dropdown-item" href="{{ route('cip-staff-submissions') }}">Submissions</a>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Marketing Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('staff-markets-manage-data') }}">Manage Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('staff-markets-submit-data') }}">Marketing Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Gross Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('staff-gross-margin-manage-data') }}">Manage Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('staff-gross-margin-add-data') }}">Gross Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cip-staff-reports') }}">
                                <i class='bx bx-table me-1'></i> Reports
                            </a>
                        </li>
                    </ul>
                @endhasallroles

                @hasallroles('project_manager')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('project_manager-dashboard') }}">
                                <i class='bx bx-tachometer me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class='bx bx-folder me-1'></i> Data Management
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ route('project_manager-indicators') }}">Indicators</a></li>
                                <li><a class="dropdown-item" href="{{ route('project_manager-forms') }}">Form Data</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('project_manager-targets') }}">View
                                        Targets</a></li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Marketing Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('project_manager-markets-manage-data') }}">Manage Data</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Gross Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('project_manager-gross-margin-manage-data') }}">Manage
                                                Data</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('project_manager-reports') }}">
                                <i class='bx bx-table me-1'></i> Reports
                            </a>
                        </li>
                    </ul>
                @endhasallroles

                @hasallroles('enumerator')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('enumerator-dashboard') }}">
                                <i class='bx bx-tachometer me-1'></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('enumerator-submissions') }}">
                                <i class='bx bx-bar-chart-alt-2 me-1'></i> Submissions
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class='bx bx-folder me-1'></i> Data Management
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Marketing Management</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="{{ route('enumerator-markets-manage-data') }}">Manage Data</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('enumerator-markets-submit-data') }}">Marketing Data
                                                Submission</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endhasallroles

            </div>

            {{-- Right side: search, notifications, profile — stays visible even when collapsed --}}
            <div class="order-2 gap-2 d-flex align-items-center order-lg-3 ms-auto">

                <div class="dropdown d-none">
                    <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class='bx bx-search fs-3 text-muted'></i>
                    </button>
                    <div class="p-0 dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <form class="p-3">
                            <div class="search-box">
                                <div class="position-relative">
                                    <input type="text" class="rounded form-control" placeholder="Search here...">
                                    <i class="mdi mdi-magnify search-icon"></i>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <livewire:user-notification-component />

                <div class="dropdown d-inline-block">
                    <button type="button" class="p-0 border-0 btn header-item d-flex align-items-center"
                        id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <img class="rounded-circle header-profile-user"
                            src="{{ auth()->user()->image != null || auth()->user()->image != '' ? asset('storage/profiles/' . auth()->user()->image) : asset('assets/images/users/usr.png') }}"
                            alt="Header Avatar">
                    </button>
                    <div class="pt-0 dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}</h6>

                        @php
                            $routePrefixMain = \Illuminate\Support\Facades\Route::current()->getPrefix();
                        @endphp

                        <a class="dropdown-item" href="{{ $routePrefixMain . '/profile' }}">
                            <i class='align-middle bx bx-user-circle text-muted font-size-18 me-1'></i>
                            <span class="align-middle">My Account</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class='align-middle bx bx-log-out text-muted font-size-18 me-1'></i>
                                <span class="align-middle">{{ __('Log Out') }}</span>
                            </a>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.dropdown-submenu > .dropdown-toggle')
            .forEach(function(toggle) {

                toggle.addEventListener('click', function(e) {

                    if (window.innerWidth >= 992) {
                        return;
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    const submenu = this.nextElementSibling;

                    // Close sibling submenus only
                    const parentMenu = this.closest('.dropdown-menu');
                    parentMenu.querySelectorAll(':scope > .dropdown-submenu > .dropdown-menu.show')
                        .forEach(function(openMenu) {
                            if (openMenu !== submenu) {
                                openMenu.classList.remove('show');
                                openMenu.parentElement.classList.remove('show');
                            }
                        });

                    submenu.classList.toggle('show');
                    this.parentElement.classList.toggle('show');
                });

            });

        // Prevent Bootstrap from closing while navigating submenus
        document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
            menu.addEventListener('click', function(e) {
                if (window.innerWidth < 992) {
                    e.stopPropagation();
                }
            });
        });

    });
</script>
