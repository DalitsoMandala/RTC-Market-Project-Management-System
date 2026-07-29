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

    <!-- Indicator & Filters Toolbar -->
    <div class="my-3 row align-items-center gy-3">
        <!-- Indicator Dropdown Selector -->
        <div class="col-12 col-lg-6">
            <div class="dropdown open custom-tooltip" title="Choose different indicator">
                <a class="p-0 m-0 btn dropdown-toggle fw-semibold fs-5 text-start text-break text-wrap" type="button"
                    id="triggerId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $indicator_name }} ({{ $indicator_no }}) <i class="ms-2 bx bx-chevron-down"></i>
                </a>
                <div class="shadow dropdown-menu w-100" aria-labelledby="triggerId"
                    style="max-height: 350px; overflow-y: auto;">
                    @php
                        $user = auth()->user()->roles()->first()->name ?? '';
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
                    @foreach (\App\Models\Indicator::where('is_active', 1)->get() as $ind)
                        <a class="dropdown-item text-wrap" href="{{ route($link, ['id' => $ind->id]) }}">
                            ({{ $ind->indicator_no }})
                            {{ $ind->indicator_name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="col-12 col-lg-6">
            <div
                class="gap-2 d-flex flex-column flex-md-row justify-content-lg-end align-items-start align-items-lg-center">

                <!-- Filter Icon Label -->
                <div class="d-flex align-items-center text-muted">
                    <i class="bx bx-filter fs-5 me-1"></i>
                    <span class="fw-semibold text-dark">Filters</span>
                </div>

                <!-- Filters Toolbar Group -->
                <div class="flex-wrap gap-2 d-flex w-100 w-md-auto justify-content-lg-end">

                    <!-- Organisation Filter -->
                    <div class="dropdown flex-fill @if (auth()->user()->hasAnyRole('external')) d-none @endif"
                        x-data="{
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
                        <a class="px-3 dropdown-toggle btn btn-warning btn-sm fw-bolder w-100 d-flex justify-content-between align-items-center"
                            href="#" id="orgDropdown" :class="{ 'opacity-25 pe-none': disable }"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="font-size-12 text-uppercase text-truncate me-2">Org: <span class="fw-medium"
                                    x-text="Organisation.name"></span></span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="shadow dropdown-menu dropdown-menu-end" aria-labelledby="orgDropdown">
                            <template x-for="(value, index) in Organisations" :key="value.id">
                                <a class="dropdown-item" @click="setData(value)" href="#"
                                    :class="{ 'disabled': Organisation.name === value.name }">
                                    <span x-text="value.name"></span>
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Project Year Filter -->
                    <div class="dropdown flex-fill" x-data="{
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
                        <a class="px-3 dropdown-toggle btn btn-warning btn-sm fw-bolder w-100 d-flex justify-content-between align-items-center"
                            href="#" id="yearDropdown" :class="{ 'opacity-25 pe-none': disable }"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="font-size-12 text-uppercase text-truncate me-2">Year: <span class="fw-medium"
                                    x-text="'Year '+financialYear.number"></span></span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="shadow dropdown-menu dropdown-menu-end" aria-labelledby="yearDropdown">
                            <template x-for="(value, index) in financialYears" :key="value.id">
                                <a class="dropdown-item" @click="setData(value)" href="#"
                                    :class="{ 'disabled': financialYear.number === value.number }">
                                    <span x-text="'Year '+ value.number"></span>
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Enterprise Filter -->
                    <div class="dropdown flex-fill" x-data="{
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
                        <a class="px-3 dropdown-toggle btn btn-warning btn-sm fw-bolder w-100 d-flex justify-content-between align-items-center"
                            href="#" id="cropDropdown" :class="{ 'opacity-25 pe-none': disable }"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="font-size-12 text-uppercase text-truncate me-2">Enterprise: <span
                                    class="fw-medium" x-text="selectedCrop.name"></span></span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="shadow dropdown-menu dropdown-menu-end" aria-labelledby="cropDropdown">
                            <template x-for="(value, index) in crops" :key="index">
                                <a class="dropdown-item" @click="setData(value)" href="#"
                                    :class="{ 'disabled': selectedCrop.value === value.value }">
                                    <span x-text="value.name"></span>
                                </a>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
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
                <div class="py-5 d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-warning spinner-border-lg" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
