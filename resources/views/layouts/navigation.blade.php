<div class="d-none">
    @include('layouts.other-navs')
</div>

<style>
    .custom-container {
        max-width: 85%;
    }
</style>

<header id="page-topbar" class="ishorizontal-topbar border-top">

    <nav class="bg-white border-bottom d-block " id="topbar" x-data>
        <div
            class="container gap-1 px-4 mx-auto custom-container d-flex flex-column flex-lg-row justify-content-between align-items-center">
            <a routerlink="/" class="text-black navbar-brand d-flex align-items-center " href="/">
                <x-application-logo width="50" /><span class="ms-2 fw-medium ">{{ config('app.name') }}</a>


            {{-- URL contains /dashboard --}}
            @php
                $routePrefix = trim(\Illuminate\Support\Facades\Route::current()->getPrefix(), '/');
            @endphp
            @hasanyrole('admin|manager|project_manager|staff')
                <!-- Right side: Dashboard links -->
                <ul class="gap-2 nav align-items-center justify-content-center topbar-nav" x-data="{
                    role: '{{ Auth::user()->getRoleNames()->first() }}',
                    makeActive(value) {
                
                        if (value == 1) {
                            document.getElementById('dashboard-one').classList.add('active');
                            document.getElementById('dashboard-two').classList.remove('active');
                        } else if (value == 2) {
                            document.getElementById('dashboard-one').classList.remove('active');
                            document.getElementById('dashboard-two').classList.add('active');
                
                        }
                    },
                
                }">



                    <li class="nav-item">
                        <a href="{{ url('/') }}" id="dashboard-one"
                            class="px-3 btn btn-sm {{ request()->is($routePrefix . '/dashboard') ? 'btn-outline-warning active' : 'btn-light' }}">
                            Project Report
                        </a>
                    </li>



                    <li class="nav-item">
                        <a href="{{ url($routePrefix . '/dashboard-2') }}" id="dashboard-two"
                            class="px-3 btn btn-sm {{ request()->is($routePrefix . '/dashboard-2') ? 'btn-outline-warning active' : 'btn-light' }}">
                            Market Data
                        </a>
                    </li>



                    <li class="nav-item">
                        <a href="{{ url($routePrefix . '/dashboard-3') }}" id="dashboard-three"
                            class="px-3 btn btn-sm {{ request()->is($routePrefix . '/dashboard-3') ? 'btn-outline-warning active' : 'btn-light' }}">
                            Gross Margins
                        </a>
                    </li>

                </ul>
            @endhasanyrole

      

        </div>
    </nav>
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->


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
                                        id="topnav-dashboard" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class='mb-1 bx bx-home'></i>
                                        Dashboard
                                    </a>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('admin-users') }}"
                                        id="topnav-dashboard" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class='mb-1 bx bx-user'></i>
                                        Manage Users
                                    </a>
                                </li>



                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" aria-expanded="false">
                                        <i class="mb-1 bx bx-folder"></i>
                                        Data Management

                                    </a>
                                    <ul class="dropdown-menu">



                                        <div class="dropdown">
                                            <a class="dropdown-item" href="#">
                                                Project Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">


                                                <a href="{{ route('admin-period') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Reporting Periods</a>


                                            </div>
                                        </div>



                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                role="button">
                                                Indicator Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('admin-indicators') }}" class="dropdown-item"
                                                    data-key="t-lightbox">Indicators</a>

                                                <a class="dropdown-item" href="/admin/baseline" data-key="t-range-slider">

                                                    Manage Baseline Data
                                                </a>
                                                <a href="{{ route('admin-std-targets') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Indicator Targets</a>


                                                <a href="{{ route('admin-targets') }}" class="dropdown-item"
                                                    data-key="t-range-slider">View Targets</a>

                                                <a href="{{ route('admin-sources') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Indicator Sources</a>


                                            </div>
                                        </div>



                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                role="button">
                                                Operations Management

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
                                                role="button">
                                                Marketing Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('admin-markets-manage-data') }}" class="dropdown-item"
                                                    data-key="t-lightbox">Manage Data</a>
                                                <a href="{{ route('admin-markets-submit-data') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Marketing Data Submission</a>


                                            </div>
                                        </div>

                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                role="button">
                                                Gross Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('admin-gross-margin-manage-data') }}"
                                                    class="dropdown-item" data-key="t-lightbox">Manage Data</a>
                                                <a href="{{ route('admin-gross-margin-add-data') }}"
                                                    class="dropdown-item" data-key="t-range-slider">Gross Data
                                                    Submission</a>


                                            </div>
                                        </div>

                                    </ul>


                                </li>
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('admin-setup') }}"
                                        id="topnav-dashboard" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class='mb-1 bx bx-cog'></i>
                                        Settings
                                    </a>
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
                                        <i class='mb-1 bx bx-home'></i>
                                        Dashboard
                                    </a>
                                </li>



                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        aria-expanded="false">
                                        <i class="mb-1 bx bx-folder"></i>
                                        Data Management

                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-more">
                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-baseline') }}" role="button">
                                            Baseline Data

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-indicators') }}" role="button">
                                            Indicators

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-forms') }}" role="button">
                                            Forms

                                        </a>
                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-submission-period') }}" role="button">
                                            Submission Periods

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-targets') }}" role="button">
                                            View Targets

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-submissions') }}" role="button">
                                            Submissions

                                        </a>

                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                Marketing Management< </a>
                                                    <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                        <a href="{{ route('cip-markets-manage-data') }}"
                                                            class="dropdown-item" data-key="t-lightbox">Manage Data</a>
                                                        <a href="{{ route('cip-markets-submit-data') }}"
                                                            class="dropdown-item" data-key="t-range-slider">Marketing Data
                                                            Submission</a>


                                                    </div>
                                        </div>

                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                Gross Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('cip-gross-margin-manage-data') }}"
                                                    class="dropdown-item" data-key="t-lightbox">Manage Data</a>
                                                <a href="{{ route('cip-gross-margin-add-data') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Gross Data Submission</a>


                                            </div>
                                        </div>
                                    </div>
                                </li>



                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('cip-reports') }}"
                                        id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        Reports
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
                                        Dashboard
                                    </a>
                                </li>


                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        aria-expanded="false">
                                        <i class="mb-1 bx bx-folder"></i>
                                        Data Management

                                    </a>
                                    <div class="dropdown-menu">


                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('external-indicators') }}" role="button">
                                            Indicators

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('external-forms') }}" role="button">
                                            Forms

                                        </a>


                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('external-targets') }}" role="button">
                                            View Targets

                                        </a>


                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('external-submission-period') }}" role="button">
                                            Submission Periods

                                        </a>
                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('external-submissions') }}" role="button">
                                            Submissions

                                        </a>
                                        {{-- @if (auth()->user()->hasAnyRole('external') && auth()->user()->organisation->name === 'RTCDT')
                                            <a class="dropdown-item dropdown-toggle arrow-none"
                                                href="/external/products/upload-data"  role="button">
                                                Upload Products Data

                                            </a>

                                            <a class="dropdown-item dropdown-toggle arrow-none"
                                                href="/external/products/view-data"  role="button">
                                                View Products Data

                                            </a>
                                        @endif --}}


                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('external-reports') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        Reports
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
                                        Dashboard
                                    </a>
                                </li>



                                <li class="nav-item dropdown">
                                     <a class="nav-link dropdown-toggle" href="#" role="button"
                                        aria-expanded="false">
                                        <i class="mb-1 bx bx-folder"></i>
                                        Data Management

                                    </a>
                                    <div class="dropdown-menu">

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-indicators') }}" role="button">
                                            Indicators

                                        </a>



                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-forms') }}" role="button">
                                            Forms

                                        </a>


                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-targets') }}" role="button">
                                            View Targets

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-submission-period') }}" role="button">
                                            Submission Periods

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-staff-submissions') }}" role="button">
                                            Submissions

                                        </a>


                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                Marketing Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('staff-markets-manage-data') }}" class="dropdown-item"
                                                    data-key="t-lightbox">Manage Data</a>
                                                <a href="{{ route('staff-markets-submit-data') }}" class="dropdown-item"
                                                    data-key="t-range-slider">Marketing Data Submission</a>


                                            </div>
                                        </div>

                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                Gross Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('staff-gross-margin-manage-data') }}"
                                                    class="dropdown-item" data-key="t-lightbox">Manage Data</a>
                                                <a href="{{ route('staff-gross-margin-add-data') }}"
                                                    class="dropdown-item" data-key="t-range-slider">Gross Data
                                                    Submission</a>


                                            </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('cip-staff-reports') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        Reports
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
                                        Dashboard
                                    </a>
                                </li>


                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        aria-expanded="false">
                                        <i class="mb-1 bx bx-folder"></i>
                                        Data Management

                                    </a>


                                    <div class="dropdown-menu" aria-labelledby="topnav-more">

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('project_manager-indicators') }}" role="button">
                                            Indicators

                                        </a>


                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('project_manager-forms') }}" role="button">
                                            Forms

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('project_manager-targets') }}" role="button">
                                            View Targets

                                        </a>

                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                Marketing Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('project_manager-markets-manage-data') }}"
                                                    class="dropdown-item" data-key="t-lightbox">Manage Data</a>


                                            </div>
                                        </div>


                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                Gross Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('project_manager-gross-margin-manage-data') }}"
                                                    class="dropdown-item" data-key="t-lightbox">Manage Data</a>



                                            </div>
                                        </div>

                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('project_manager-reports') }}" id="topnav-dashboard"
                                        role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-table'></i>
                                        Reports
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                @endhasallroles

                @hasallroles('enumerator')
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('enumerator-dashboard') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-tachometer'></i>
                                        Dashboard
                                    </a>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('enumerator-submissions') }}" id="topnav-dashboard"
                                        role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class='bx bx-bar-chart-alt-2 '></i>
                                        Submissions
                                    </a>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        aria-expanded="false">
                                        <i class="mb-1 bx bx-folder"></i>
                                        Data Management

                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="topnav-more">





                                        <div class="dropdown">
                                            <a class="dropdown-item dropdown-toggle arrow-none" href="#"
                                                id="topnav-extended" role="button">
                                                Marketing Management

                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="topnav-form">
                                                <a href="{{ route('enumerator-markets-manage-data') }}"
                                                    class="dropdown-item" data-key="t-lightbox">Manage Data</a>

                                                <a href="{{ route('enumerator-markets-submit-data') }}"
                                                    class="dropdown-item" data-key="t-lightbox">Marketing Data
                                                    Submission</a>

                                            </div>
                                        </div>

                                    </div>
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
                        src="{{ auth()->user()->image != null || auth()->user()->image != '' ? asset('storage/profiles/' . auth()->user()->image) : asset('assets/images/users/usr.png') }}"
                        alt="Header Avatar">
                </button>
                <div class="pt-0 dropdown-menu dropdown-menu-end">
                    <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}</h6>

                    @php
                        $routePrefixMain = \Illuminate\Support\Facades\Route::current()->getPrefix();
                    @endphp

                    <a class="dropdown-item" href="{{ $routePrefixMain . '/profile' }}"><i
                            class='align-middle bx bx-user-circle text-muted font-size-18 me-1'></i> <span
                            class="align-middle">My Account</a>

                    <div class="dropdown-divider"></div>


                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            <i class='align-middle bx bx-log-out text-muted font-size-18 me-1'></i> <span
                                class="align-middle"> {{ __('Log Out') }}

                        </a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</header>
