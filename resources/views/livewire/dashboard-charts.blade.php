<div>




    <div class="my-4 ">
        <div class="my-2 row align-items-center">
            <div class="col">
                <h2 class="h2">Summary</h2>
                <p class="text-muted"><span x-text="name"></span></p>
            </div>
            <div class="col ">

                <div class="d-flex justify-content-end" wire:ignore x-data="{
                
                    selectedReportYear: $wire.entangle('selectedReportYear'),
                    financialYears: $wire.entangle('financialYears'),
                    showContent: $wire.entangle('showContent'),
                    changeYear(data) {
                        $wire.dispatch('updateReportYear', {
                            id: data.id,
                        });
                    },
                
                }">
                    <div class="dropdown card-header-dropdown"
                        :class="{
                            'pe-none opacity-25': showContent === false
                        }">
                        <a class="shadow-none dropdown-btn btn btn-soft-warning waves-effect waves-light" href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-filter me-5 fw-bold"></i> <span> <span id='report_year'
                                    x-text="'Year ' + selectedReportYear"></span> <i
                                    class="mdi mdi-chevron-down ms-1"></i></span>

                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="">
                            <template x-for="(value, index) in financialYears" :key="value.id">
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    x-on:click="changeYear(value)"
                                    :class="{
                                        'disabled': value.number === selectedReportYear
                                    }"
                                    x-text="'Year ' + value.number"></a>
                            </template>

                        </div>
                    </div>

                </div>

            </div>
        </div>





        @if (!$showContent)
            <div x-data x-init="() => {
                setTimeout(() => {
            
                    $wire.dispatch('showCharts');
                }, 4000)
            }">

                <div class="d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-warning spinner-border-lg" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

            </div>
        @else
            <livewire:charts-view :data="$data" />
        @endif


    </div>



</div>
