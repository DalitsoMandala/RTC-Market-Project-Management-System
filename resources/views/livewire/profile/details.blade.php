<div>
    @section('title')
        Profile
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">

                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">My Profile</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row ">
            <div class="col-12 ">

                <div class="row">
                    <div class="col-12">


                        <div class="card">
                            <x-card-header>My Profile Information</x-card-header>
                            <div class="card-body">
                                @if ($form_top)
                                    <x-alerts />
                                @endif
                                <form wire:submit.prevent="saveProfile">
                                    @if (session()->has('profile_message'))
                                        <div class="alert alert-success">
                                            {{ session('profile_message') }}
                                        </div>
                                    @endif

                                    <div class="mb-3 form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control bg-light" id="email"
                                            wire:model="email" readonly>
                                    </div>

                                    <div class="mb-3 form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" wire:model="username">
                                        @error('username')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 form-group">
                                        <label for="profile_image">Profile Image</label>

                                        <div class="gap-1 row justify-content-center justify-content-md-start ">
                                            <div class="col-12 col-xl-1 ">

                                                @if (auth()->user()->image)
                                                    <img src="{{ asset('storage/profiles/' . auth()->user()->image) }}"
                                                        class="rounded avatar-xl" alt=" ">
                                                @else
                                                    <img src="{{ asset('assets/images/users/usr.png') }}"
                                                        class="rounded avatar-xl" alt="logo">
                                                @endif
                                            </div>

                                            <div class="my-2 col-12 col-xl-11">
                                                <x-profile-image instantUpload="true" type="file"
                                                    class="form-control-file" id="profile_image"
                                                    wire:model="profile_image" />
                                            </div>
                                        </div>

                                        @error('profile_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 form-group">
                                        <label for="organization">Organization</label>
                                        <input type="text" class="form-control bg-light" readonly id="organization"
                                            wire:model="organization">
                                        @error('organization')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-warning">Save Info</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <x-card-header>Security Settings</x-card-header>
                            <div class="card-body">
                                @if (!$form_top)
                                    <x-alerts />
                                @endif
                                <form wire:submit.prevent="saveSecurity">
                                    @if (session()->has('security_message'))
                                        <div class="alert alert-success">
                                            {{ session('security_message') }}
                                        </div>
                                    @endif

                                    @if (session()->has('security_error'))
                                        <div class="alert alert-danger">
                                            {{ session('security_error') }}
                                        </div>
                                    @endif

                                    <div class="mb-3 form-group">
                                        <label for="old_password">Old Password</label>
                                        <input type="password" class="form-control" id="old_password"
                                            wire:model="old_password">
                                        @error('old_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control" id="new_password"
                                            wire:model="new_password">
                                        @error('new_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3 form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                            wire:model="confirm_password">
                                        @error('confirm_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-warning">Save Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        {{-- <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




    </div>

</div>
