<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Users</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Users</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card" x-data="{
                    showForm: false,
                    resetForm() {
                        $wire.dispatch('resetForm');
                    }
                }" @edit.window="showForm=true;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">User Table</h4>
                        <button class="btn btn-warning" @click="showForm= true; resetForm()">Add <i
                                class="bx bx-plus"></i></button>
                    </div>

                    <div class="card-header" x-show="showForm">
                        <x-alerts />
                        <form wire:submit="save">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" wire:model="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" wire:model="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" wire:model="phone">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="organisation" class="form-label">Organisation</label>
                                <select class="form-select @error('organisation') is-invalid @enderror"
                                    id="organisation" wire:model="organisation">
                                    <option value="" disabled>Select an organisation</option>
                                    @foreach ($organisations as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                </select>
                                @error('organisation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" x-data="{
                                //  role: $wire.entangle('role').live
                            }">
                                <label for="role" class="form-label">Roles</label>
                                <select class="form-select @error('role') is-invalid @enderror" multiple id="role"
                                    wire:model="role">
                                    <option value="" disabled>Select a role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}">{{ str_replace('_', ' ', $role) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div x-data="{ changePassword: $wire.entangle('changePassword') }">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label for="password" class="form-label">Password</label>
                                        <a href="#" data-bs-toggle="modal" x-show="!changePassword"
                                            @click="changePassword = true">Change
                                            password</a>
                                        <a href="#" data-bs-toggle="modal" x-show="changePassword"
                                            class="text-danger" @click="changePassword = false">Cancel</a>
                                    </div>

                                    <input type="password"
                                        class="form-control @error('password') is-invalid @enderror  "
                                        :disabled="changePassword == false" id="password" wire:model="password"
                                        :class="{ 'bg-light': changePassword == false }">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control " :disabled="changePassword == false"
                                        :class="{ 'bg-light': changePassword == false }" id="password_confirmation"
                                        wire:model="password_confirmation">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning goUp">
                                @if ($rowId)
                                    Update
                                @else
                                    Submit
                                @endif
                            </button>
                            <button type="button" class="btn btn-light"
                                @click="showForm = false; resetForm(); ">Close</button>

                        </form>
                    </div>
                    <div class="px-0 card-body">
                        <livewire:admin.user-table />
                    </div>
                </div>
            </div>



            <div x-data x-init="$wire.on('showModal-delete', (e) => {
            
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            })">


                <x-modal id="view-delete-modal" title="Delete record">
                    <form>

                        <p class="mb-0 text-center">Are you sure you want to delete this record?</p>
                        <p class="text-center ">ID: {{ $this->rowId }}</p>
                        <div class="gap-1 mt-3 d-flex justify-content-center">
                            <button type="button" class="px-5 btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="px-5 btn btn-theme-red"
                                wire:click='deleteUser'>Yes</button>

                        </div>
                    </form>
                </x-modal>

            </div>

            <div x-data x-init="$wire.on('showModal-restore', (e) => {
            
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            })
            
            
            $wire.on('refresh', (e) => {
                const modals = document.querySelectorAll('.modal.show');
            
                // Iterate over each modal and hide it using Bootstrap's modal hide method
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            })">


                <x-modal id="view-restore-modal" title="Restore record">
                    <form>
                        <p class="mb-0 text-center">Are you sure you want to restore this record?</p>
                        <p class="text-center ">ID: {{ $this->rowId }}</p>
                        <div class="gap-1 mt-3 d-flex justify-content-center">
                            <button type="button" class="px-5 btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="px-5 btn btn-success" wire:click='restoreUser'>Yes</button>

                        </div>
                    </form>
                </x-modal>

            </div>



        </div>

    </div>
    @script
        <script>
            $('.goUp').on('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                })
            });
        </script>
    @endscript
