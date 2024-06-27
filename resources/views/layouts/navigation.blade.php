@include('layouts.other-navs')
<header id="page-topbar" class="ishorizontal-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">

                <a href="index.html" class="logo">

                    <span class="logo-xl">
                        <x-application-logo width="22" /> <span class="logo-txt">CDMS</span>
                    </span>
                </a>
            </div>

            <button type="button" class="px-3 btn btn-sm font-size-16 d-lg-none header-item" data-bs-toggle="collapse"
                data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="topnav">
                @hasallroles('internal|cip|organiser')
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">
                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('cip-internal-dashboard') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class='bx bx-tachometer'></i>
                                        <span data-key="t-dashboards">Dashboard</span>
                                    </a>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('cip-internal-indicators') }}" id="topnav-dashboard" role="button"
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
                                            href="{{ route('cip-internal-forms') }}" id="topnav-utility" role="button">
                                            <span data-key="t-utility">Forms</span>

                                        </a>
                                        <a class="dropdown-item dropdown-toggle arrow-none"
                                            href="{{ route('cip-internal-submission-period') }}" id="topnav-utility"
                                            role="button">
                                            <span data-key="t-utility">Submission Periods</span>

                                        </a>

                                        <a class="dropdown-item dropdown-toggle arrow-none" href="submissions"
                                            id="topnav-utility" role="button">
                                            <span data-key="t-utility">Submissions</span>

                                        </a>


                                    </div>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none "
                                        href="{{ route('cip-internal-reports') }}" id="topnav-dashboard" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                    <a class="nav-link dropdown-toggle arrow-none " href="{{ route('external-dashboard') }}"
                                        id="topnav-dashboard" role="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
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
                                        <a class="dropdown-item dropdown-toggle arrow-none" href="/external/submissions"
                                            id="topnav-utility" role="button">
                                            <span data-key="t-utility">My Submissions</span>

                                        </a>



                                    </div>
                                </li>

                                <li class="nav-item ">
                                    <a class="nav-link dropdown-toggle arrow-none " href="#" id="topnav-dashboard"
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

            <div class="dropdown d-inline-block d-none ">
                <button type="button" class="btn header-item noti-icon" id="page-header-notifications-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                    <i class='bx bxs-bell fs-3 text-muted'></i>
                    <span class="noti-dot bg-danger rounded-pill">3</span>
                </button>
                <div class="p-0 dropdown-menu dropdown-menu-lg dropdown-menu-end"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="m-0 font-size-15"> Notifications </h5>
                            </div>
                            <div class="col-auto">
                                <a href="javascript:void(0);" class="small"> Mark all as read</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 250px;">
                        <a href="#" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start bg-light">
                                <div class="flex-shrink-0">
                                    <img src="assets/images/users/avatar-3.jpg" class="me-3 rounded-circle avatar-sm"
                                        alt="user-pic">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Justin Verduzco</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Your task changed an issue from "In
                                            Progress" to <span
                                                class="badge text-success bg-success-subtle">Review</span></p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 1 hour ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            <i class="bx bx-shopping-bag"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">New order has been placed</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Open the order confirmation or shipment
                                            confirmation.</p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 5 hours ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm me-3">
                                        <span
                                            class="avatar-title bg-success-subtle text-success rounded-circle font-size-16">
                                            <i class="bx bx-cart"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Your item is shipped</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Here is somthing that you might light like
                                            to know.</p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 1 day ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="text-reset notification-item">
                            <div class="d-flex border-bottom align-items-start">
                                <div class="flex-shrink-0">
                                    <img src="assets/images/users/avatar-4.jpg" class="me-3 rounded-circle avatar-sm"
                                        alt="user-pic">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Salena Layfield</h6>
                                    <div class="text-muted">
                                        <p class="mb-1 font-size-13">Yay ! Everything worked!</p>
                                        <p class="mb-0 font-size-10 text-uppercase fw-bold"><i
                                                class="mdi mdi-clock-outline"></i> 3 days ago</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="text-center btn btn-sm btn-link font-size-14 btn-block text-decoration-underline fw-bold"
                            href="javascript:void(0)">
                            <span>View All <i class='bx bx-right-arrow-alt'></i></span>
                        </a>
                    </div>
                </div>
            </div>
            <livewire:user-notification-component />

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ asset('assets/images/users/avatar-4.jpg') }}" alt="Header Avatar">
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
