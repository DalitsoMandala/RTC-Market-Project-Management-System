<div>
    @section('title')
        Forms
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Forms</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">


            <div class="col-12">
                <x-alerts />

                <div class="border-0 shadow-sm card">
                    <x-card-header
                        class="flex-wrap gap-3 py-3 bg-white d-flex align-items-center justify-content-between">
                        <!-- Card Title -->
                        <div class="mb-0 h5 card-title fw-semibold text-dark d-flex align-items-center">
                            <i class="bx bx-file me-2 text-muted fs-4"></i>
                            <span>Forms List</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex-wrap gap-2 d-flex align-items-center">
                            @hasanyrole('monitor|admin|manager')
                                {{-- Add Form Button --}}
                                <button type="button"
                                    class="gap-1 shadow-sm btn btn-warning btn-sm d-inline-flex align-items-center"
                                    wire:loading.attr="disabled" wire:click="createForm">
                                    <i class="bx bx-plus fs-5"></i>
                                    <span>Add Form</span>
                                </button>

                                {{-- Download Button --}}
                                <button type="button"
                                    class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1 {{ $downloading ? 'disabled' : '' }}"
                                    wire:click="downloadForms" wire:loading.attr="disabled" wire:target="downloadForms">
                                    <i class="bx bx-download fs-5" wire:loading.remove wire:target="downloadForms"></i>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                        wire:loading wire:target="downloadForms"></span>
                                    <span>{{ $downloading ? 'Exporting...' : 'Download Forms' }}</span>
                                </button>

                                {{-- Download Ready Notice --}}
                                @if ($downloadReady)
                                    <a href="#" wire:click="downloadFile" wire:loading.attr="disabled"
                                        class="gap-1 shadow-sm btn btn-success btn-sm d-inline-flex align-items-center"
                                        title="Download Export File">
                                        <i class="bx bx-download fs-5"></i>
                                        <span>Download ZIP</span>
                                    </a>
                                @endif

                                {{-- Polling while exporting --}}
                                @if ($downloading)
                                    <div wire:poll.2000ms="pollForDownload"></div>
                                @endif
                            @endhasanyrole
                        </div>
                    </x-card-header>

                    <div class="p-4 card-body">
                        <livewire:tables.form-table />
                    </div>
                </div>
            </div>
        </div>





        <div x-data x-init="$wire.on('showModal', (e) => {
        
            const myModal = new bootstrap.Modal(document.getElementById('form-modal'), {})
            myModal.show();
        })
        $wire.on('hideModal', (e) => {
            const modals = document.querySelectorAll('.modal.show');
        
            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        })">


            <x-modal id="form-modal" title="{{ $editing ? 'Edit Form' : 'Add New Form' }}">

                <form wire:submit="save">

                    {{-- Form Name --}}
                    <div class="mb-3" x-data>
                        <label class="form-label">
                            Form Name
                        </label>

                        <x-text-input wire:keypress='' placeholder="Enter form name..." wire:model="name" />

                        @error('name')
                            <span class="my-1 text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>


                    {{-- Project --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Project
                        </label>

                        <select class="form-select" wire:model="project_id">
                            <option value="">Select Project</option>

                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('project_id')
                            <span class="my-1 text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>


                    {{-- Footer --}}
                    <div class="modal-footer border-top-0">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            Close
                        </button>

                        <button type="submit" class="btn btn-warning" wire:loading.attr="disabled">

                            <span wire:loading.remove wire:target="save">
                                {{ $editing ? 'Save Changes' : 'Create Form' }}
                            </span>

                            <span wire:loading wire:target="save">
                                Saving...
                            </span>

                        </button>

                    </div>

                </form>

            </x-modal>

        </div>




    </div>

</div>
