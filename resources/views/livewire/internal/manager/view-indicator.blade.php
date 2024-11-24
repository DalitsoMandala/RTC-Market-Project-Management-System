<div>

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">View Indicator</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/cip/indicators">Indicators</a></li>
                            <li class="breadcrumb-item active">View Indicator</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header d-flex flex-column flex-md-row  justify-content-between">
                        <h5>{{ $indicator_no }} - {{ $indicator_name }}</h5>

                        <div class="row">

                            <div class="col">

                                <div class="dropdown" x-data="{
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


                                }">


                                    <a class="dropdown-toggle text-reset" href="#" id="dropdownMenuButton1"
                                        :class="{
                                            'opacity-25 pe-none': disable
                                        }"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="text-muted font-size-12 text-uppercase">Organisation:</span> <span
                                            class="fw-medium">
                                            <span x-text="Organisation.name"></span>

                                            <i class="mdi mdi-chevron-down ms-1"></i></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                        <template x-for="(value, index) in Organisations" :key="value.id">
                                            <a class="dropdown-item" @click="setData(value)" href="#"> <span
                                                    x-text="value.name"></span></a>

                                        </template>

                                    </div>
                                </div>

                            </div>
                            <div class="col d-none">

                                <div class="dropdown" x-data="{
                                    reportingPeriod: $wire.entangle('selectedReportingPeriod'),
                                    reportingPeriods: $wire.entangle('reportingPeriod'),
                                    setData(value) {
                                        this.disable = true;
                                        setTimeout(() => {
                                            this.reportingPeriod = value;
                                            $wire.dispatch('refreshData');
                                            this.disable = false;
                                        }, 1000);

                                    },


                                }">


                                    <a class="dropdown-toggle text-reset" href="#" id="dropdownMenuButton1"
                                        :class="{
                                            'opacity-25 pe-none': disable
                                        }"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="text-muted font-size-12 text-uppercase">Period of:</span> <span
                                            class="fw-medium">
                                            <span
                                                x-text="reportingPeriod.start_month + ' - ' + reportingPeriod.end_month"></span>

                                            <i class="mdi mdi-chevron-down ms-1"></i></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                        <template x-for="(value, index) in reportingPeriods" :key="value.id">
                                            <a class="dropdown-item" @click="setData(value)" href="#"> <span
                                                    x-text="value.start_month + ' - '+ value.end_month"></span></a>

                                        </template>

                                    </div>
                                </div>

                            </div>
                            <div class="col">

                                <div class="dropdown" x-data="{
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


                                    <a class="dropdown-toggle text-reset" href="#" id="dropdownMenuButton1"
                                        :class="{
                                            'opacity-25 pe-none': disable
                                        }"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="text-muted font-size-12 text-uppercase">Project Year:</span> <span
                                            class="fw-medium">
                                            <span x-text="'Year '+financialYear.number"></span>

                                            <i class="mdi mdi-chevron-down ms-1"></i></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                        <template x-for="(value, index) in financialYears" :key="value.id">
                                            <a class="dropdown-item" @click="setData(value)" href="#"> <span
                                                    x-text="'Year '+ value.number"></span></a>

                                        </template>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-body">


                        @if ($component)
                            @livewire($component, [
                                'indicator_no' => $indicator_no,
                                'indicator_name' => $indicator_name,
                                'indicator_id' => $indicator_id,
                                'project_id' => $project_id,

                                'financial_year' => $selectedFinancialYear,
                                'organisation' => $selectedOrganisation,
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
        </div>




    </div>



</div>

</div>
