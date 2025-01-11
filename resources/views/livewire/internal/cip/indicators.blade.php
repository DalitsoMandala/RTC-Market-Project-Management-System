<div>


    <div class="container-fluid">

        <!-- start page title -->
        <div class="my-2 row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Indicators</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Indicators</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row animate__animated animate__fadeIn">
            <div class="col-12">
                <x-alerts />
                <div class="card">
                    <div class="card-header fw-bold">
                        Indicators Table
                    </div>
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

                        <div class="alert alert-warning">{{ $indicator }}</div>

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
                        <button type="submit" class="btn btn-warning">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>







    </div>

</div>
