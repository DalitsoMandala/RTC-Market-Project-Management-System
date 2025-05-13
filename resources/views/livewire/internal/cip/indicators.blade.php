<div>

    @section('title')
        Indicators
    @endsection
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
                    <div class="card-header">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                    aria-selected="true" wire:ignore.self>
                                    Indicators
                                </button>
                            </li>
                            @hasanyrole('admin|manager')
                                <li class="nav-item " role="presentation">
                                    <button class="nav-link " id="profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#profile" type="button" role="tab" aria-controls="profile"
                                        aria-selected="false" wire:ignore.self>
                                        Disaggregations
                                    </button>
                                </li>
                            @endhasanyrole
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" wire:ignore.self id="home" role="tabpanel"
                                aria-labelledby="home-tab">
                                <div class="mt-2 card-header fw-bold">
                                    <h5 class="card-title">Indicators Table</h5>

                                </div>
                                <div class="px-0 card-body">
                                    <livewire:tables.indicatorTable :userId="auth()->user()->id" />
                                </div>
                            </div>
                            <div class="tab-pane" wire:ignore.self id="profile" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <form wire:submit='saveDisaggregations' class="my-3">

                                    <div class="mt-2 row">
                                        <h5 class="card-title">New Disaggregation</h5>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="" class="form-label">Name</label>
                                                <input type="text" wire:loading.attr="disabled" wire:model='name'
                                                    class="form-control @error('name') is-invalid @enderror">
                                                @error('name')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="" class="form-label">Assigned Indicator</label>
                                                <select class="form-select " wire:loading.attr="disabled"
                                                    wire:model.live.debounce.1000ms="selectedIndicator">
                                                    <option value="">Select one</option>
                                                    @foreach ($indicators as $indicator)
                                                        <option value="{{ $indicator->id }}">
                                                            ({{ $indicator->indicator_no }})
                                                            {{ $indicator->indicator_name }}</option>
                                                    @endforeach
                                                </select>

                                                @error('selectedIndicator')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <!-- Horizontal under breakpoint -->
                                            <ul class="mb-3 list-group" wire:loading.class="opacity-25 pe-nonw">
                                                <li class="list-group-item bg-warning-subtle">Available Disaggregations
                                                </li>
                                                @foreach ($disaggregations as $disaggregation)
                                                    <li class="list-group-item">{{ $disaggregation->name }}</li>
                                                @endforeach
                                            </ul>

                                        </div>
                                    </div>


                                    <button type="submit" class="px-5 btn btn-warning">Submit</button>

                                </form>

                            </div>

                        </div>
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
