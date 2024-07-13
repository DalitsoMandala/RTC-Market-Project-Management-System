<div>

    <style>
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            display: flex;
            flex-direction: row;

            padding-right: 10px;
            margin-right: .375rem;
            margin-bottom: .375rem;
            font-size: 12px;
            color: #212529;
            cursor: auto;

            border-radius: 10rem;
            background-color: #3980c0;
            border-color: #3980c0;
            color: #fff;
            word-break: break-all;
            box-sizing: border-box;
            font-weight: 500;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
            width: .75rem;
            height: .75rem;
            padding: .55em;
            margin-right: .25rem;
            overflow: hidden;
            text-indent: 100%;
            white-space: nowrap;
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23FFFFFF'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") 50%/.75rem auto no-repeat;

            border: 0;
        }
    </style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Indicators</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Indicators</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                @if (session()->has('success'))
                    <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                @endif


                @if (session()->has('error'))
                    <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
                @endif
                <div class="card">
                    <div class="card-body">
                        <livewire:tables.indicatorTable :userId="auth()->user()->id" />
                    </div>
                </div>
            </div>
        </div>


        <div x-data x-init="$wire.on('showModal', (e) => {
            $wire.setData(e.rowId);
            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
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


            <x-modal id="view-indicator-modal" title="Update data">
                <form wire:submit='save'>
                    <div class="mb-3">

                        <div class="alert alert-primary">{{ $indicator }}</div>

                        <x-text-input hidden placeholder="Name of indicator..." wire:model='indicator' readonly />
                        @error('indicator')
                            <span class="my-1 text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">

                        <label for="project-select" class="form-label"> Project</label>
                        <select id="project-select" class="form-select form-select-md"
                            wire:model.live.debounce.500ms="selectedProject" disabled>
                            <option selected value="">Select one</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedProject')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>


                    <div class="mb-1" wire:ignore x-data="{
                    
                        myInput(data) {
                                this.selected = data;
                            },
                    
                    }" x-init="$('#selectElementPartner').select2({
                        width: '100%',
                        theme: 'bootstrap-5',
                        containerCssClass: 'select2--small',
                        dropdownCssClass: 'select2--small',
                    });
                    
                    
                    $('#selectElementPartner').on('change', function() {
                    
                        data = $(this).val();
                    
                        $wire.selectedLeadPartner = data;
                    
                    
                    });
                    
                    
                    
                    $wire.on('select-partners', (e) => {
                        data = e.data;
                        $('#selectElementPartner').val(data).trigger('change');
                    
                    
                    
                    
                    })">


                        <label for="" class="form-label">Lead Partners</label>
                        <select class="form-select form-select-md" multiple id="selectElementPartner">
                            @foreach ($leadPartners as $key => $partner)
                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                            @endforeach
                        </select>


                    </div>
                    @error('selectedLeadPartner')
                        <x-error>{{ $message }}</x-error>
                    @enderror

                    <div class="mb-1" wire:ignore x-data="{
                        selected: {},
                        myInput(data) {
                            this.selected = data;
                        },
                    
                    }" x-init="$('#selectSource').select2({
                        width: '100%',
                        theme: 'bootstrap-5',
                        containerCssClass: 'select2--small',
                        dropdownCssClass: 'select2--small',
                    });
                    
                    
                    $('#selectSource').on('change', function() {
                    
                        data = $(this).val();
                        $wire.selectedSource = data;
                        // console.log(selected)
                    
                    });
                    
                    
                    $wire.on('select-partners', (e) => {
                        data = e.data2;
                        $('#selectSource').val(data).trigger('change');
                    
                    
                    
                    
                    })">


                        <label for="" class="form-label">Sources</label>
                        <select class="form-select form-select-md" multiple id="selectSource" x-model="selected">
                            @foreach ($sources as $key => $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>


                    </div>
                    @error('selectedSource')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>







    </div>

</div>
