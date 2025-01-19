<x-guest-layout>
    <!-- Session Status -->

    <div class="authentication-bg min-vh-100" style=" bottom;">
        <div class="bg-overlay bg-light"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="text-center home-wrapper">
                        <div>
                            <div class="row justify-content-center">
                                <div class="col-sm-9">
                                    @yield('error-img')

                                </div>
                            </div>
                        </div>

                        <h4 class="mt-5 text-uppercase"> @yield('code')</h4>
                        <p class="text-muted">@yield('message')</p>
                        <div class="mt-2">
                            <a class="btn btn-warning waves-effect waves-light" href="/"> <i
                                    class="bx bx-home"></i> Back to Dashboard</a>

                            <form class="d-inline-flex" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="btn btn-theme-red" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                    <i class='align-middle bx bx-log-out font-size-18 me-1'></i> <span
                                        class="align-middle">
                                        {{ __('Log Out') }}</span>

                                </a>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>


</x-guest-layout>
