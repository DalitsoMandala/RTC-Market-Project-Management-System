<div class="d-none">
    @include('layouts.other-navs')
</div>



<header id="page-topbar" class="border-top">

    @php
        $user = auth()->user();
        $role = \App\Services\Navigation::primaryRole($user);
        $dashboardTabs = collect(config("navigation.dashboard_tabs.{$role}", []))->filter(
            fn($tab) => \Illuminate\Support\Facades\Route::has($tab['route']),
        );

        $profileRouteName = config("navigation.profile_routes.{$role}");
        if ($profileRouteName && !\Illuminate\Support\Facades\Route::has($profileRouteName)) {
            $profileRouteName = \Illuminate\Support\Facades\Route::has('profile') ? 'profile' : null;
        }
    @endphp

    {{-- ============ ROW 1: brand + dashboard switch buttons ============ --}}
    <div class="py-2 bg-white border-bottom" id="topbar-row1">
        <div
            class="gap-2 container-fluid custom-container d-flex flex-column flex-lg-row justify-content-between align-items-center">

            <div class="d-flex justify-content-between align-items-center gap-1">
                <a class="mb-0 text-black navbar-brand d-flex align-items-center px-2" href="https://cipotato.org">
                    <img src="{{ asset('assets/images/cip-logo.png') }}" alt="cip-logo" width="100" />
                </a>

                <div class="vr"></div>

                <a class="mb-0 text-black navbar-brand d-flex align-items-center px-2" href="/">
                    <x-application-logo width="42" />
                    <span class="ms-2 fw-semibold">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

                {{-- Dashboard switch tabs — only renders if this role has any of them --}}
                @if ($dashboardTabs->isNotEmpty())
                    <ul class="nav flex-wrap align-items-center gap-2 mb-0">
                        @foreach ($dashboardTabs as $tab)
                            <li class="nav-item">
                                <a href="{{ route($tab['route']) }}" id="dashboard-{{ $loop->index + 1 }}"
                                    class="btn btn-sm px-3 btn-outline-warning {{ request()->routeIs($tab['route']) ? 'active' : '' }}">
                                    <i class="bx {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Notifications + profile — for every authenticated user --}}
                @auth
                    <div class="d-flex align-items-center gap-2 ms-auto">

                        <livewire:user-notification-component />

                        <div class="dropdown d-inline-block position-relative">
                            <button type="button" class="btn d-flex align-items-center p-0 border-0"
                                id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <img class="rounded-circle header-profile-user"
                                    src="{{ $user->image ? asset('storage/profiles/' . $user->image) : asset('assets/images/users/usr.png') }}"
                                    alt="User avatar">
                                <i class="bx bx-chevron-down fs-5 text-muted"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end pt-0">
                                <h6 class="dropdown-header">{{ $user->name }}</h6>

                                @if ($profileRouteName)
                                    <a class="dropdown-item" href="{{ route($profileRouteName) }}">
                                        <i class="bx bx-user-circle text-muted font-size-18 me-1"></i>
                                        <span>My Account</span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bx bx-log-out text-muted font-size-18 me-1"></i>
                                        <span>{{ __('Log Out') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth

            </div>
        </div>
    </div>

    {{-- ============ ROW 2: main navigation ============ --}}
    <nav class="bg-white navbar navbar-expand-lg navbar-light navbar-bottom border-bottom">
        <div class="flex-wrap container-fluid custom-container d-flex align-items-center">

            <button class="order-1 navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#topnav-menu-content" aria-controls="topnav-menu-content" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="order-3 collapse navbar-collapse order-lg-2 flex-grow-1" id="topnav-menu-content">
                <x-main-nav :items="\App\Services\Navigation::forUser($user)" />
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
