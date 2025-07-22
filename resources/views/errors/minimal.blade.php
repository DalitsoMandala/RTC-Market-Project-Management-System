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

                        <h4 class=" text-uppercase display-5"> @yield('code')</h4>
                        <p class="text-muted" style="font-size:16px">@yield('message')</p>
                        <div class="mt-2">
                            @php
                                $errorCode = trim($__env->yieldContent('code'));
                            @endphp

                            @if ($errorCode !== 'Site Under Maintenance')
                                <form class="gap-2 d-inline-flex" method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <a class="btn btn-warning waves-effect waves-light" href="/"> Back to
                                        Dashboard</a>

                                    <button type="button" class="btn btn-secondary"
                                        onclick="event.preventDefault();
                                        this.closest('form').submit();">

                                        {{ __('Log Out') }}

                                    </button>

                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>


</x-guest-layout>
