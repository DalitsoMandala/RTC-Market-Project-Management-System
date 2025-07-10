<style>
    .topbar-nav .nav-item .nav-link {
        color: #ffffffde !important;
    }

    .topbar-nav .nav-item .nav-link:hover {
        color: #ffffff !important;
        background-color: #ffffff1a !important;
        border-radius: 5px;
        font-weight: medium;
    }

    .topbar-nav .nav-item .nav-link:active,
    .topbar-nav .nav-item .nav-link.active {
        color: #ffffff !important;
        background-color: #ffffff1a !important;
        border-radius: 5px;
        font-weight: medium;
    }
</style>
<header id="dashboard-2" class="topBar">

    <nav class="py-2 bg-warning border-bottom " x-data x-init="() => {
    
    
    
    
    
    }">
        <div class="container flex-wrap d-flex justify-content-center justify-content-md-between align-items-center"
            style="max-width: 85%;">

            <!-- Left side: App name -->
            <ul class="nav">
                <li class="nav-item">
                    <a href="#" class="px-2 text-center disabled nav-link text-light fw-bold text-uppercase">
                        {{ config('app.name') }}
                    </a>
                </li>
            </ul>

            @php
                use Illuminate\Support\Str;
            @endphp

            @if (Str::contains(request()->url(), '/dashboard'))
                {{-- URL contains /dashboard --}}

                @hasanyrole('admin|manager|project_manager|enumarator')
                    <!-- Right side: Dashboard links -->
                    <ul class="nav align-items-center topbar-nav" x-data="{
                        makeActive(value) {
                    
                            if (value == 1) {
                                document.getElementById('dashboard-one').classList.add('active');
                                document.getElementById('dashboard-two').classList.remove('active');
                            } else if (value == 2) {
                                document.getElementById('dashboard-one').classList.remove('active');
                                document.getElementById('dashboard-two').classList.add('active');
                            }
                        }
                    }">

                        <li class="nav-item">
                            <a href="#dashboard-1" id="dashboard-one"
                                @change-dashboard.window="makeActive($event.detail.value)"
                                @click="$dispatch('change-dashboard', { value: 1 });"
                                class="px-3 active nav-link text-light">
                                Project Report
                            </a>
                        </li>
                        <li class="mx-2 nav-item text-light">|</li>
                        <li class="nav-item">
                            <a href="#dashboard-2" id="dashboard-two"
                                @change-dashboard.window="makeActive($event.detail.value)"
                                @click="$dispatch('change-dashboard', { value: 2 })"
                                class="px-3 nav-link text-light ">Market Data
                            </a>
                        </li>
                    </ul>
                @endhasanyrole
            @endif
        </div>
    </nav>

</header>
