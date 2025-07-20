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
                                    <h5 class="text-warning">Forgot Password!</h5>
                                    <div class="mt-4 alert alert-warning">
                                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                                    </div>

                                </div>

                                <x-auth-session-status class="mb-4" :status="session('status')" />
                                <div class="p-2 mt-4">
                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf

                                        <!-- Email Address -->
                                        <div>
                                            <x-input-label for="email" :value="__('Email')" />
                                            <x-text-input id="email" class="block w-full mt-1" type="email" name="email"
                                                :value="old('email')" required autofocus />
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>

                                        <div class="my-4 text-center">
                                            <x-primary-button class="btn btn-warning w-sm waves-effect waves-light"
                                                type="submit"> {{ __('Email Password Reset Link') }}</x-primary-button>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div><!-- end col -->
                </div><!-- end row -->

            </div>
        </div><!-- end container -->
    </div>



</x-guest-layout>
