@include('layouts.user-navigation')

@include('layouts.other-navs')


<header id="page-topbar" class="ishorizontal-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">

                <a href="/" class="logo">

                    <span class="logo-xl">
                        <x-application-logo width="50" />
                    </span>
                </a>
            </div>

            @php
                $routePrefixMain = \Illuminate\Support\Facades\Route::current()->getPrefix();
            @endphp


            <button type="button" class="px-3 btn btn-sm font-size-16 d-lg-none header-item" data-bs-toggle="collapse"
                data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="topnav">
                @hasallroles('admin')
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('admin-dashboard') }}"
                                        id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-tachometer'></i>
                                        <span data-key="t-dashboards">Dashboard</span>
                                    </a>
                                </li>


                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                        role="button">
                                        <i class="bx bx-file"></i>
                                        <span data-key="t-pages">Manage Users</span>
                                        <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">




                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('admin-users') }}" id="topnav-utility" role="button">
                                            <span data-key="t-utility">List Users</span>

                                        </a>


                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('admin-user-roles') }}" id="topnav-utility" role="button">
                                            <span data-key="t-utility">User Roles</span>

                                        </a>
                                    </div>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                        role="button">
                                        <i class="bx bx-file"></i>
                                        <span data-key="t-pages">Data Management</span>
                                        <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">



                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                <span data-key="t-extendeds">Project Management</span>
                                                <div class="arrow-down"></div>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">


                                                <a href="{{ route('admin-period') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Reporting Periods</a>


                                            </div>
                                        </div>



                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                <span data-key="t-extendeds">Indicator Management</span>
                                                <div class="arrow-down"></div>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('admin-indicators') }}" class="dropdown-item"
                                                    data-key="t-lightbox">Indicators</a>

                                                <a class="dropdown-item" href="/admin/baseline" data-key="t-range-slider">

                                                    <span data-key="t-dashboards">Manage Baseline Data</span>
                                                </a>

                                                {{-- <a href="{{ route('admin-leads') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Lead partners</a> --}}

                                                <a href="{{ route('admin-sources') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Organisation Forms</a>
                                                {{-- <a href="{{ route('admin-indicators-targets') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Indicator Targets</a>

                                                <a href="{{ route('admin-assigned-targets') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Assigned Targets</a> --}}
                                            </div>
                                        </div>



                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                <span data-key="t-extendeds">Operations Management</span>
                                                <div class="arrow-down"></div>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('admin-forms') }}" class="dropdown-item"
                                                    data-key="t-lightbox">Forms</a>

                                                <a href="{{ route('admin-submissions') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Submissions</a>
                                                <a href="{{ route('admin-submission-period') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Submission Periods</a>
                                                <a href="{{ route('admin-reports') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Reports</a>

                                            </div>
                                        </div>

                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                <span data-key="t-extendeds">Marketing Management</span>
                                                <div class="arrow-down"></div>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('admin-forms') }}" class="dropdown-item"
                                                    data-key="t-lightbox">Manage Data</a>
                                                <a href="{{ route('admin-submissions') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Marketing Data Submission</a>


                                            </div>
                                        </div>



                                    </div>


                                </li>


                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                        role="button">
                                        <i class="bx bx-file"></i>
                                        <span data-key="t-pages">System settings</span>
                                        <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">


                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('admin-setup') }}" id="topnav-utility" role="button">
                                            <span data-key="t-utility">System setup</span>

                                        </a>



                                    </div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                @endhasallroles

                @hasallroles('manager')
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('cip-dashboard') }}"
                                        id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-tachometer'></i>
                                        <span data-key="t-dashboards">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ $routePrefixMain }}/baseline" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                        <span data-key="t-dashboards">Manage Baseline Data</span>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('cip-indicators') }}"
                                        id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-bar-chart-alt-2 '></i>
                                        <span data-key="t-dashboards">Indicators</span>
                                    </a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                        role="button">
                                        <i class="bx bx-file"></i>
                                        <span data-key="t-pages">Manage Data</span>
                                        <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">




                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-forms') }}" id="topnav-utility" role="button">
                                            <span data-key="t-utility">Forms</span>

                                        </a>
                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-submission-period') }}" id="topnav-utility"
                                            role="button">
                                            <span data-key="t-utility">Submission Periods</span>

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-submissions') }}" id="topnav-utility" role="button">
                                            <span data-key="t-utility">Submissions</span>

                                        </a>


                                    </div>
                                </li>



                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('cip-reports') }}"
                                        id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        <span data-key="t-dashboards">Reports</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                @endhasallroles
                @hasallroles('external')
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('external-dashboard') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-tachometer'></i>
                                        <span data-key="t-dashboards">Dashboard</span>
                                    </a>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('external-indicators') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-bar-chart-alt-2 '></i>
                                        <span data-key="t-dashboards">Indicators</span>
                                    </a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                        role="button">
                                        <i class="bx bx-file"></i>
                                        <span data-key="t-pages">Manage Data</span>
                                        <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">



                                        <a class="dropdown-item dropdown-toggle arrow-none" href="/external/forms"
                                            id="topnav-utility" role="button">
                                            <span data-key="t-utility">Forms</span>

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="/external/submission-periods" id="topnav-utility" role="button">
                                            <span data-key="t-utility">Submission Periods</span>

                                        </a>
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="/external/submissions"
                                            id="topnav-utility" role="button">
                                            <span data-key="t-utility">Submissions</span>

                                        </a>



                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('external-reports') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        <span data-key="t-dashboards">Reports</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                @endhasallroles

                @hasallroles('staff')
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('cip-staff-dashboard') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-tachometer'></i>
                                        <span data-key="t-dashboards">Dashboard</span>
                                    </a>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('cip-staff-indicators') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-bar-chart-alt-2 '></i>
                                        <span data-key="t-dashboards">Indicators</span>
                                    </a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                        role="button">
                                        <i class="bx bx-file"></i>
                                        <span data-key="t-pages">Manage Data</span>
                                        <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">



                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-forms') }}" id="topnav-utility" role="button">
                                            <span data-key="t-utility">Forms</span>

                                        </a>
                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-submission-period') }}" id="topnav-utility"
                                            role="button">
                                            <span data-key="t-utility">Submission Periods</span>

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-submissions') }}" id="topnav-utility"
                                            role="button">
                                            <span data-key="t-utility">Submissions</span>

                                        </a>


                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('cip-staff-reports') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        <span data-key="t-dashboards">Reports</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                @endhasallroles


                @hasallroles('project_manager')
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('project_manager-dashboard') }}" id="topnav-dashboard"
                                        role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-tachometer'></i>
                                        <span data-key="t-dashboards">Dashboard</span>
                                    </a>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('project_manager-indicators') }}" id="topnav-dashboard"
                                        role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-bar-chart-alt-2 '></i>
                                        <span data-key="t-dashboards">Indicators</span>
                                    </a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more"
                                        role="button">
                                        <i class="bx bx-file"></i>
                                        <span data-key="t-pages">Manage Data</span>
                                        <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">



                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('project_manager-forms') }}" id="topnav-utility"
                                            role="button">
                                            <span data-key="t-utility">Forms</span>

                                        </a>



                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('project_manager-reports') }}" id="topnav-dashboard"
                                        role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        <span data-key="t-dashboards">Reports</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                @endhasallroles
            </div>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block d-none">
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
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ auth()->user()->image != null ? asset('storage/profiles/' . auth()->user()->image) : asset('assets/images/users/usr.png') }}"
                        alt="Header Avatar">
                </button>
                <div class="pt-0 dropdown-menu dropdown-menu-end">
                    <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}</h6>
                    <a class="dropdown-item" href="/profile"><i
                            class='align-middle bx bx-user-circle text-muted font-size-18 me-1'></i> <span
                            class="align-middle">My Account</span></a>

                    <div class="dropdown-divider"></div>


                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            <i class='align-middle bx bx-log-out text-muted font-size-18 me-1'></i> <span
                                class="align-middle"> {{ __('Log Out') }}</span>

                        </a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</header>
