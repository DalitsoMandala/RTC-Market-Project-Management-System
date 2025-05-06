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
                                    <h5 class="text-warning">Welcome!</h5>
                                    <p class="text-muted">Sign in </p>
                                </div>
                                @if ($errors->has('login'))

                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->get('login') as $message)
                                                <li>{{ $message }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                @endif
                                <div class="p-2 mt-4">

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        <div class="mb-3">
                                            <x-input-label for="email" :value="__('Email')" />
                                            <x-text-input id="email" class="" type="text" name="email"
                                                required autofocus placeholder="Enter your email" />


                                        </div>

                                        <div class="mb-3">
                                            <x-input-label for="password" :value="__('Password')" />

                                            <x-text-input id="password" class="" type="password" name="password"
                                                placeholder="Enter your password" required
                                                autocomplete="current-password" />

                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>


                                        <div class="d-flex justify-content-between">
                                            <div class="form-check d-none">
                                                <input type="checkbox" class="form-check-input" id="auth-remember-check"
                                                    name="remember">
                                                <label class="form-check-label" for="auth-remember-check">Remember
                                                    me</label>
                                            </div>

                                            @if (Route::has('password.request'))
                                                <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                    href="{{ route('password.request') }}">
                                                    {{ __('Forgot your password?') }}
                                                </a>
                                            @endif
                                        </div>

                                        <div class="mt-5 mb-5 text-center">
                                            <x-primary-button class="mx-5 btn btn-warning w-sm waves-effect waves-light"
                                                type="submit">Log In</x-primary-button>
                                        </div>



                                        <div class="mt-4 text-center d-none">
                                            <p class="mb-0">Don't have an account ? <a href="auth-register.html"
                                                    class="fw-medium text-warning"> Signup now </a> </p>
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
