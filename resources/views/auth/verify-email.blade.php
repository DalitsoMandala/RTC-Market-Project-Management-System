<x-guest-layout>
    <!-- Session Status -->



    <div class="authentication-bg min-vh-100">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="px-3 pt-4 d-flex flex-column min-vh-100">
                <div class="my-auto row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">

                        <div class="mb-4 text-center">
                            <a href="/">
                                <x-application-logo width="150" />
                                <br>
                                <span class="mt-5 logo-txt">{{ config('app.name') }}</span>
                            </a>
                        </div>

                        <div class="card">
                            <div class="p-4 card-body">
                                <div class="mt-2 text-center">
                                    <h5 class="text-warning">Verify Your Email!</h5>

                                    <div class="mt-4 alert alert-warning">
                                        {{ __('We need you to verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                                    </div>
                                    @if (session('status') == 'verification-link-sent')
                                        <div class="mb-4 alert alert-success">
                                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                        </div>
                                    @endif

                                </div>


                                <div class="p-2 mt-4 mb-5">


                                    <div class=" d-flex justify-content-center align-items-center">
                                        <form method="POST" action="{{ route('verification.send') }}">
                                            @csrf

                                            <div>
                                                <x-primary-button class="me-1">
                                                    {{ __('Resend Verification Email') }}
                                                </x-primary-button>
                                            </div>
                                        </form>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf

                                            <button type="submit" class="btn btn-secondary">
                                                {{ __('Log Out') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div><!-- end col -->
                </div><!-- end row -->

            </div>
        </div><!-- end container -->
    </div>



</x-guest-layout>
