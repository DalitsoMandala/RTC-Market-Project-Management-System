<style>
    .topbar-nav .nav-item .nav-link {
        color: #7b8190 !important;
        margin: 0rem 0.2rem;
    }

    .topbar-nav .nav-item .nav-link:hover {
        color: #FC931D !important;
        background-color: #ff9d001a !important;
        border-radius: 5px;
        font-weight: medium;
    }

    .topbar-nav .nav-item .nav-link:active,
    .topbar-nav .nav-item .nav-link.active {
        color: #FC931D !important;
        background-color: #ff9d001a !important;
        border-radius: 5px;
        font-weight: medium;
    }
</style>
<header id="dashboard-2" class="topBar">

    <nav class="py-2 bg-white " x-data x-init="() => {





    }">
        <div class="container flex-wrap d-flex justify-content-center justify-content-md-between align-items-center"
            style="max-width: 85%;">

            <!-- Left side: App name -->
            <ul class="nav">
                <li class="nav-item">
                    <a href="#"  class="px-2 text-center disabled nav-link fw-bold text-uppercase text-muted">
                        {{ config('app.name') }}
                    </a>
                </li>
            </ul>


            {{-- URL contains /dashboard --}}
            @php
                $routePrefix = trim(\Illuminate\Support\Facades\Route::current()->getPrefix(), '/');
            @endphp
            @hasanyrole('admin|manager|project_manager|staff')
                <!-- Right side: Dashboard links -->
                <ul class="nav align-items-center topbar-nav" x-data="{
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
                            class="px-3 nav-link text-secondary {{ request()->is($routePrefix . '/dashboard') ? 'active' : '' }}">
                            Project Report
                        </a>
                    </li>



                    <li class="nav-item">
                        <a href="{{ url($routePrefix . '/dashboard-2') }}" id="dashboard-two"
                            class="px-3 nav-link text-ligsecondaryht {{ request()->is($routePrefix . '/dashboard-2') ? 'active' : '' }}">
                            Market Data
                        </a>
                    </li>



                    <li class="nav-item">
                        <a href="{{ url($routePrefix . '/dashboard-3') }}" id="dashboard-three"
                            class="px-3 nav-link text-secondary {{ request()->is($routePrefix . '/dashboard-3') ? 'active' : '' }}">
                            Gross Margins
                        </a>
                    </li>

                </ul>
            @endhasanyrole

            @hasanyrole('enumerator')
                <!-- Right side: Dashboard links -->
                <ul class="nav align-items-center topbar-nav">

                    <li class="nav-item">
                        <a href="/" id="dashboard-one"
                            class="px-3 nav-link text-light {{ request()->is($routePrefix . '/dashboard') ? 'active' : '' }} ">
                            Project Report
                        </a>
                    </li>

                </ul>
            @endhasanyrole

        </div>
    </nav>

</header>
