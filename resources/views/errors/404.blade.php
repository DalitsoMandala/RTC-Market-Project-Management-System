<x-guest-layout>
    <!-- Session Status -->

    <div class="authentication-bg min-vh-100" style="background: url(./assets/images/auth-bg.jpg) bottom;">
        <div class="bg-overlay bg-light"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="home-wrapper text-center">
                        <div>
                            <div class="row justify-content-center">
                                <div class="col-sm-9">
                                    <div class="error-img">
                                        <img src="assets/images/404-img.png" alt=""
                                            class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="text-uppercase mt-5">Sorry, page not found</h4>
                        <p class="text-muted">The page you have requested doesn't exist</p>
                        <div class="mt-5">
                            <a class="btn btn-primary waves-effect waves-light" href="/">Back to Dashboard</a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    {{-- @section('title', __('Not Found'))
    @section('code', '404')
    @section('message', __('Not Found')) --}}

</x-guest-layout>
