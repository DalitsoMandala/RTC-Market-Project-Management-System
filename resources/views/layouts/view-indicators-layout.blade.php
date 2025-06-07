<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">View Indicator</h4>

                <div class="page-title-right">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        @hasallroles('cip|admin')
                            <li class="breadcrumb-item"><a href="/admin/indicators">Indicators</a></li>
                        @endrole
                        @hasallroles('cip|manager')
                            <li class="breadcrumb-item"><a href="/cip/indicators">Indicators</a></li>
                        @endrole
                        @hasallroles('cip|project_manager')
                            <li class="breadcrumb-item"><a href="/project_manager/indicators">Indicators</a></li>
                        @endrole
                        @hasallroles('cip|staff')
                            <li class="breadcrumb-item"><a href="/staff/indicators">Indicators</a></li>
                        @endrole

                        @hasallroles('external')
                            <li class="breadcrumb-item"><a href="/external/indicators">Indicators</a></li>
                        @endrole


                        <li class="breadcrumb-item active">View Indicator</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="my-2 row">
        <div class="col-12 col-md-8">
            <h5 class="mb-3 h3">{{ $indicator_name }} ({{ $indicator_no }} )</h5>
        </div>
        <div class="flex-wrap col-12 col-md-4 d-flex justify-content-end ">
            <div class="dropdown mb-1 @if (auth()->user()->hasAnyRole('external')) d-none @endif" x-data="{
                Organisation: $wire.entangle('selectedOrganisation'),
                Organisations: $wire.entangle('organisations'),
                setData(value) {
                    this.disable = true;
                    setTimeout(() => {
                        this.Organisation = value;
                        $wire.dispatch('refreshData');
                        this.disable = false;
                    }, 1000);

                },

                disable: false


            }">


                <a class="dropdown-toggle btn btn-soft-warning me-2 fw-bolder" href="#" id="dropdownMenuButton1"
                    :class="{
                        'opacity-25 pe-none': disable
                    }"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class=" font-size-12 text-uppercase me-3">Organisation:</span> <span class="fw-medium">
                        <span x-text="Organisation.name"></span>

                        <i class="mdi mdi-chevron-down ms-1"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                    <template x-for="(value, index) in Organisations" :key="value.id">
                        <a class="dropdown-item" @click="setData(value)" href="#" data-bs-toggle="modal"
                            :class="{
                                'disabled': Organisation.name === value.name
                            }">
                            <span x-text="value.name"></span></a>

                    </template>

                </div>
            </div>
            <div class="mb-1 dropdown" x-data="{
                financialYear: $wire.entangle('selectedFinancialYear'),
                financialYears: $wire.entangle('financialYears'),
                setData(value) {
                    this.disable = true;
                    setTimeout(() => {
                        this.financialYear = value;
                        $wire.dispatch('refreshData');
                        this.disable = false;
                    }, 1000);






                },

                disable: false

            }">


                <a class="dropdown-toggle btn btn-soft-warning ms-2 fw-bolder" href="#" id="dropdownMenuButton1"
                    :class="{
                        'opacity-25 pe-none': disable
                    }"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="font-size-12 text-uppercase me-3">Project Year:</span> <span class="fw-medium">
                        <span x-text="'Year '+financialYear.number"></span>

                        <i class="mdi mdi-chevron-down ms-1"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                    <template x-for="(value, index) in financialYears" :key="value.id">
                        <a class="dropdown-item" @click="setData(value)" href="#" data-bs-toggle="modal"
                            :class="{
                                'disabled': financialYear.number === value.number
                            }">
                            <span x-text="'Year '+ value.number"></span></a>

                    </template>

                </div>
            </div>

    <div class="mb-1 dropdown" x-data="{
                selectedCrop: $wire.entangle('selectedCrop'),
                crops: $wire.entangle('crops'),
                setData(value) {

                    this.disable = true;
                    setTimeout(() => {
                        this.selectedCrop = value;
                        $wire.dispatch('refreshData');
                        this.disable = false;
                    }, 1000);






                },

                disable: false

            }">


                <a class="dropdown-toggle btn btn-soft-warning ms-2 fw-bolder" href="#" id="dropdownMenuButton1"
                    :class="{
                        'opacity-25 pe-none': disable
                    }"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="font-size-12 text-uppercase me-3">Crop:</span> <span class="fw-medium">
                        <span x-text="selectedCrop.name"></span>

                        <i class="mdi mdi-chevron-down ms-1"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                    <template x-for="(value, index) in crops" :key="index">
                        <a class="dropdown-item" @click="setData(value)" href="#" data-bs-toggle="modal"
                            :class="{
                                'disabled': selectedCrop.value === value.value
                            }">
                            <span x-text="value.name"></span></a>

                    </template>

                </div>
            </div>


        </div>
    </div>
    <div class="row">
        <div class="col-12">








            @if ($component)
                @livewire($component, [
                    'indicator_no' => $indicator_no,
                    'indicator_name' => $indicator_name,
                    'indicator_id' => $indicator_id,
                    'project_id' => $project_id,

                    'financial_year' => $selectedFinancialYear,
                    'organisation' => $selectedOrganisation,
                    'crop' => $selectedCrop['value']
                ])
            @else
                <div class="d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-warning spinner-border-lg" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            @endif


        </div>
    </div>




</div>
