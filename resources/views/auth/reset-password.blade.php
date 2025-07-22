<x-guest-layout>


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
                                    <h5 class="text-warning">Reset Password!</h5>

                                </div>

                                <x-auth-session-status class="mb-4" :status="session('status')" />
                                <div class="p-2 mt-4">
                                    <form method="POST" action="{{ route('password.store') }}">
                                        @csrf

                                        <!-- Password Reset Token -->
                                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                        <!-- Email Address -->
                                        <div>
                                            <x-input-label for="email" :value="__('Email')" />
                                            <x-text-input readonly id="email" class="block w-full mt-1" type="email"
                                                name="email" :value="old('email', $request->email)" required autofocus
                                                autocomplete="username" />
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>

                                        <!-- Password -->
                                        <div class="mt-4">
                                            <x-input-label for="password" :value="__('Password')" />
                                            <x-text-input id="password" class="block w-full mt-1" type="password"
                                                name="password" required autocomplete="new-password" />
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="mt-4">
                                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                                            <x-text-input id="password_confirmation" class="block w-full mt-1"
                                                type="password" name="password_confirmation" required
                                                autocomplete="new-password" />

                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>

                                        <div class="mt-4 text-center">
                                            <x-primary-button class=" btn btn-warning">
                                                {{ __('Reset Password') }}
                                            </x-primary-button>
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
