<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">


                <div class="page-title-left col-12">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        @hasallroles('admin')
                            <li class="breadcrumb-item"><a href="/admin/indicators">Indicators</a></li>
                        @endrole
                        @hasallroles('manager')
                            <li class="breadcrumb-item"><a href="/cip/indicators">Indicators</a></li>
                        @endrole
                        @hasallroles('project_manager')
                            <li class="breadcrumb-item"><a href="/project_manager/indicators">Indicators</a></li>
                        @endrole
                        @hasallroles('staff')
                            <li class="breadcrumb-item"><a href="/staff/indicators">Indicators</a></li>
                        @endrole

                        @hasallroles('external')
                            <li class="breadcrumb-item"><a href="/external/indicators">Indicators</a></li>
                        @endrole


                        <li class="breadcrumb-item active">View Indicator ({{ $indicator_no }})</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="my-2 row">
        <div class="col-12 col-md-6 d-flex align-items-end">

            <div class="dropdown open d-flex align-items-end custom-tooltip" title="Choose different indicator">
                <a class="px-0 mb-0 btn dropdown-toggle fw-semibold fs-5" type="button" id="triggerId"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $indicator_name }} ({{ $indicator_no }} ) <i class="ms-2 bx bx-chevron-down"></i>
                </a>
                <div class="dropdown-menu " aria-labelledby="triggerId">
                    @php
                        $user = auth()->user()->roles()->first()->name;
                        $link = '';
                        switch ($user) {
                            case 'admin':
                                $link = 'admin-indicator-view';
                                break;
                            case 'manager':
                                $link = 'cip-indicator-view';
                                break;
                            case 'project_manager':
                                $link = 'project_manager-indicator-view';
                                break;
                            case 'staff':
                                $link = 'staff-indicator-view';
                                break;
                            case 'monitor':
                                $link = 'monitor-indicator-view';
                                break;
                            case 'enumerator':
                                $link = 'enumerator-indicator-view';
                                break;
                            case 'external':
                                $link = 'external-indicator-view';
                                break;
                        }

                    @endphp
                    @foreach (\App\Models\Indicator::get() as $ind)
                        <a class="dropdown-item" href="{{ route($link, ['id' => $ind->id]) }}">({{ $ind->indicator_no }})
                            {{ $ind->indicator_name }} ({{ $ind->indicator_no }})</a>
                    @endforeach
                </div>
            </div>

        </div>
        <div class="flex-wrap col-12 col-md-6 d-flex justify-content-end ">
            <div class="mb-1 d-flex justify-content-end col-12"> <i class="bx bx-filter fs-5 me-2 text-body"></i>
                <span class="fw-semibold text-dark">Filters</span>
            </div>

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


                <a class="dropdown-toggle btn btn-warning btn-sm me-2 fw-bolder" href="#" id="dropdownMenuButton1"
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


                <a class="dropdown-toggle btn btn-warning btn-sm ms-2 fw-bolder" href="#" id="dropdownMenuButton1"
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


                <a class="dropdown-toggle btn btn-warning btn-sm ms-2 fw-bolder" href="#" id="dropdownMenuButton1"
                    :class="{
                        'opacity-25 pe-none': disable
                    }"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="font-size-12 text-uppercase me-3">Enterprise:</span> <span class="fw-medium">
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
                    'crop' => $selectedCrop['value'],
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
