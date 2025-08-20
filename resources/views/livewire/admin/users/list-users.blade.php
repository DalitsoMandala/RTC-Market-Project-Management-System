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

                <ul class=" nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="batch-tab" data-bs-toggle="tab" data-bs-target="#normal"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            USERS TABLE
                        </button>
                    </li>




                </ul>
                <div class="card" x-data="{
                    showForm: false,
                    resetForm() {
                        $wire.dispatch('resetForm');

                    },
                    showUploadForm: false,

                }" @edit.window="showForm=true;">
                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>
                            <button class="px-3 btn" :class="{ 'btn-secondary': showForm, 'btn-warning': !showForm }"
                                :disabled="showUploadForm" @click="showForm= !showForm; resetForm()">
                                <span x-show="showForm">Cancel <i class="bx bx-x"></i></span> <span
                                    x-show="!showForm">Add New User <i class="bx bx-plus"></i></span></span>
                            </button>
                            <button class="px-3 btn"
                                :class="{ 'btn-secondary': showUploadForm, 'btn-warning': !showUploadForm }"
                                :disabled="showForm" @click="showUploadForm= !showUploadForm;">
                                <span x-show="showUploadForm">Cancel <i class="bx bx-x"></i></span><span
                                    x-show="!showUploadForm">Upload
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


                            <div x-data="{
                                role: $wire.entangle('role'),
                                organisation: $wire.entangle('organisation'),
                                roles: $wire.entangle('roles'),
                                organisationsData: $wire.entangle('organisations'),
                                skipValidation: false,

                                filterOrganisations(role) {
                                    if (role == null || role == '') { return this.organisationsData; }

                                    return this.organisationsData.filter(org => {
                                        return role === 'external' ?
                                            org.name !== 'CIP' :
                                            org.name === 'CIP';
                                    });
                                },
                            }" x-init="$watch('role', (newRole) => {
                                if (skipValidation) {
                                    skipValidation = false;
                                    return;
                                }

                                // Clear organisation if role is blank
                                if (newRole == '' || newRole == null) {
                                    organisation = '';
                                    return;
                                }

                                // Validate current organisation against filtered list
                                const filtered = filterOrganisations(newRole);
                                const found = filtered.find(org => org.id == organisation);
                                if (!found) {
                                    organisation = '';
                                }
                            });

                            $wire.on('update-org', (e) => {
                                skipValidation = true;

                                setTimeout(() => {
                                    role = e.role;
                                    organisation = e.organisation;
                                }, 500)

                            });">



                                <div class="mb-3">
                                    <label for="role" class="form-label">Roles</label>
                                    <select wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                        class="form-select @error('role') is-invalid @enderror" x-model="role">
                                        <option value="">Select a role</option>
                                        <template x-for="role in roles">
                                            <option :value="role"><span x-text="role"></span></option>
                                        </template>

                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3 ">
                                    <label for="organisation" class="form-label">Organisation</label>
                                    <select wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                        class="form-select @error('organisation') is-invalid @enderror"
                                        x-model="organisation">
                                        <option value="">Select an organisation</option>

                                        <template :key="org.id" x-for="org in filterOrganisations(role)">

                                            <option :value="org.id"><span x-text="org.name"></span></option>
                                        </template>

                                    </select>
                                    @error('organisation')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

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
                            <button type="button" class="btn btn-secondary"
                                @click="showForm = false; resetForm(); ">Close</button>

                        </form>
                    </div>

                    <div class="card-header" x-show="showUploadForm" x-data="uploadUsers">
                        <x-alerts />
                        <form id="uploadForm" @submit.prevent="uploadData()">
                            <div class="mb-3">
                                <label for="fileInput" class="form-label">Upload Excel File</label>
                                <input class="form-control @error('file') is-invalid @enderror " wire:model="file"
                                    type="file" id="fileInput" accept=".xlsx, .xls">
                            </div>


                            <div class="d-flex justify-content-between">
                                <div>
                                    <button @able-button.window="uploading = false" :disabled="uploading"
                                        type="button" @click.debounce.200ms="downloadTemplate()"
                                        id="downloadTemplate" wire:loading.attr='disabled' wire:target='usersData'
                                        class="btn btn-warning">Download
                                        Template</button>
                                </div>
                                <div>
                                    <button type="submit" @able-button.window="uploading = false"
                                        :disabled="uploading" class="btn btn-warning">Upload data</button>
                                    <button type="button" class="btn btn-secondary"
                                        @click="showUploadForm = false; resetForm();">Close</button>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
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

        <div class="row">
            <div class="col-12">
                <div>
                    <x-tab-component>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="batch-tab" data-bs-toggle="tab"
                                data-bs-target="#normal" type="button" role="tab" aria-controls="home"
                                aria-selected="true">
                                Send Emails
                            </button>
                        </li>
                    </x-tab-component>
                    <div class=" card">

                        <div class="card-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form wire:submit="sendEmails">
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" wire:model="subject" class="form-control @error('subject') is-invalid @enderror"
                                        placeholder="Enter email subject">
                                    @error('subject')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3" wire:ignore>
                                    <label class="form-label">Message</label>
                                <div id="editor" style="height: 250px;"></div>
                                </div>
                                @error('message')
                                    <x-error >{{ $message }}</x-error>
                                @enderror
                                <div class="mb-3">
                                    <label class="form-label">Exclude Roles</label>
                                    <div class="flex-wrap gap-3 d-flex">
                                        @foreach ($allRoles as $role)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model="excludedRoles" value="{{ $role }}">
                                                <label class="form-check-label">
                                                    {{ ucfirst($role) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="px-4 btn btn-warning">Send
                                        Emails</button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>


    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
 @endpush
    @script
        <script>
       const  quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Type your message...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        ['link', 'blockquote', 'code-block']
                    ]
                }
            });

            quill.on('text-change', function(delta, oldDelta, source) {
$wire.message = quill.root.innerHTML;
            });
            $wire.on('edit', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                })
            });

            Alpine.data('uploadUsers', () => ({
                open: false,
                uploading: false,
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
                    this.uploading = true;
                    if (!file) {
                        setTimeout(() => {
                            this.uploading = false;
                            $wire.call('noFile');
                            return;
                        }, 5000);

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
                        const jsonData = XLSX.utils.sheet_to_json(sheet, {
                            defval: null,
                            blankrows: true
                        });

                        // Map the data to the desired format
                        const users = jsonData.map(row => ({
                            email: row.email,
                            name: row.name,
                            organisation: row.organisation,
                            role: row.role,
                        }));



                        $wire.dispatch('send-users', {
                            users: users
                        });
                    };

                    reader.readAsArrayBuffer(file);


                }
            }))
        </script>
    @endscript
