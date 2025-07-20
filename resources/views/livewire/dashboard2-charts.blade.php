<div>

    <div class="my-4 ">
        <div class="my-2 row align-items-center " x-data="{

            showContent: $wire.entangle('showContent'),
        }" x-show="showContent">
            <div class="col">
                <h2 class="h2">Marketing Overview</h2>
                <p class="text-muted">Summary of Demand, Value, and Distribution Trends</p>
            </div>
            <div class="gap-1 d-flex col justify-content-end">
                <div>

                    <div class="d-flex justify-content-end" wire:ignore x-data="{
                        visible: true,
                        selectedReportYear: $wire.entangle('selectedReportYear'),
                        financialYears: $wire.entangle('financialYears'),
                        marketData: $wire.entangle('marketData'),
                        changeYear(data) {
                            this.visible = false;
                            $wire.dispatch('updateReportYear2', {
                                year: data.number,
                            });


                            setTimeout(() => {
                                this.visible = true
                            }, 5000)
                        },



                    }">
                        <div class="dropdown card-header-dropdown" :class="{ 'opacity-25 pe-none': visible === false }">
                            <a class=" btn btn-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-filter me-5 fw-bold"></i> <span> <span id='report_year'
                                        x-text="'Year - ' + selectedReportYear"></span> <i
                                        class="bx bx-chevron-down ms-1"></i></span>

                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <template x-for="(value, index) in financialYears" :key="value.id">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        x-on:click="changeYear(value)"
                                        :class="{
                                            'disabled': value.number === selectedReportYear
                                        }"
                                        x-text="'Year - ' + value.number"></a>
                                </template>

                            </div>
                        </div>

                    </div>

                </div>
                <div>
                    <div class="d-flex justify-content-end">
                        <a href="marketing/manage-data" class=" btn btn-warning">

                            View Details <i class="bx bx-arrow-to-right ms-1"></i></a>
                    </div>
                </div>

            </div>
        </div>

    </div>
    @if (!$showContent)
        <div x-data x-init="() => {
            setTimeout(() => {

                $wire.dispatch('showCharts2');
            }, 5000)
        }">



            @include('placeholders.dashboard2')
        </div>
    @else
        <livewire:charts-view-2 :data="$marketData" />
    @endif
</div>
