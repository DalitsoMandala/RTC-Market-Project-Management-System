<x-guest-layout>
    <!-- Session Status -->

    <div class="authentication-bg min-vh-100" style=" bottom;">
        <div class="bg-overlay bg-light"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="home-wrapper text-center">
                        <div>
                            <div class="row justify-content-center">
                                <div class="col-sm-9">
                                    @yield('error-img')

                                </div>
                            </div>
                        </div>

                        <h4 class="text-uppercase mt-5"> @yield('code')</h4>
                        <p class="text-muted">@yield('message')</p>
                        <div class="mt-2">
                            <a class="btn btn-primary waves-effect waves-light" href="/">Back to Dashboard</a>
                            <span class="mx-1">or</span>
                            <form class="d-inline-flex" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="btn btn-primary " href="{{ route('logout') }}" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                    <i class='align-middle bx bx-log-out  font-size-18 me-1'></i> <span
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
