<div>
    @section('title')
        Users
    @endsection
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

                    },
                    showUploadForm: false,

                }" @edit.window="showForm=true;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">User Table</h4>
                        <div>
                            <button class="px-3 btn btn-soft-warning" @click="showForm= !showForm; resetForm()">Add new
                                user
                                <i class="bx bx-plus"></i></button>
                            <button class="px-3 btn btn-soft-secondary" @click="showUploadForm= !showUploadForm;">Upload
                                new users
                                <i class="bx bx-upload"></i></button>
                        </div>
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



                            <div class="mb-3" x-data="{
                                role: $wire.entangle('role').live
                            }">
                                <label for="role" class="form-label">Roles</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role"
                                    x-model="role">
                                    <option value="">Select a role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}">{{ str_replace('_', ' ', $role) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 ">
                                <label for="organisation" class="form-label">Organisation</label>
                                <select class="form-select @error('organisation') is-invalid @enderror"
                                    id="organisation" wire:model.live.debounce.200ms="organisation">
                                    <option value="">Select an organisation</option>
                                    @foreach ($organisations as $org)
                                        @if ($disableAll)
                                            <option value="{{ $org->id }}"
                                                @if ($org->name != 'CIP') disabled @endif>{{ $org->name }}
                                            </option>
                                        @else
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('organisation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div x-data="{
                                changePassword: $wire.entangle('changePassword'),
                                edit: $wire.entangle('rowId'),
                            }">


                                <div class="mb-3">
                                    <div class="d-flex justify-content-between" x-show="edit">
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

                    <div class="card-header" x-show="showUploadForm" x-data="uploadUsers">
                        <form id="uploadForm" @submit.prevent="uploadData()">
                            <div class="mb-3">
                                <label for="fileInput" class="form-label">Upload Excel File</label>
                                <input class="form-control" type="file" id="fileInput" accept=".xlsx, .xls"
                                    required>
                            </div>
                            <button type="button" @click="downloadTemplate()" id="downloadTemplate"
                                class="btn btn-secondary">Download
                                Template</button>
                            <button type="submit" class="btn btn-warning">Upload and Parse</button>

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

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    @endpush
    @script
        <script>
            $('.goUp').on('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                })
            });

            Alpine.data('uploadUsers', () => ({
                open: false,

                downloadTemplate() {
                    const templateData = [
                        ['email', 'name', 'organisation', 'role'], // Header row
                        //     ['user1@example.com', 'John Doe', 'pass123', 'Org A', 'Admin'], // Example row
                        //  ['user2@example.com', 'Jane Smith', 'pass456', 'Org B', 'User'], // Example row
                    ];

                    const worksheet = XLSX.utils.aoa_to_sheet(templateData);

                    // Create a workbook and add the worksheet
                    const workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');

                    // Generate Excel file and trigger download
                    XLSX.writeFile(workbook, 'user_template.xlsx');
                },
                uploadData() {
                    const fileInput = document.getElementById('fileInput');
                    const file = fileInput.files[0];

                    if (!file) {
                        alert('Please select a file.');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, {
                            type: 'array'
                        });
                        const sheetName = workbook.SheetNames[0]; // Get the first sheet
                        const sheet = workbook.Sheets[sheetName];

                        // Convert the sheet to JSON
                        const jsonData = XLSX.utils.sheet_to_json(sheet);

                        // Map the data to the desired format
                        const users = jsonData.map(row => ({
                            email: row.email,
                            name: row.name,

                            organisation: row.organisation,
                            role: row.role,
                        }));

                        $wire.sendData(users);
                    };

                    reader.readAsArrayBuffer(file);


                }
            }))
        </script>
    @endscript
