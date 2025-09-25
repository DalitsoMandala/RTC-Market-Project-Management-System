<div>

    <div class="my-4 ">
        <div class="my-2 row align-items-center " x-data="{

            showContent: $wire.entangle('showContent'),
        }" x-show="showContent">
            <div class="col-12">

                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="h2">Gross Margin Overview</h2>
                        <p class="text-muted">This is a summary of gross margin</p>
                    </div>

                    <div>
                        <a href="gross-margin/manage-data" class=" btn btn-warning">

                            View Details <i
                                                class="bx bx-right-arrow-alt"></i></a>

                    </div>
                </div>

            </div>
            <div class="col-12">
                <div>

                    <div class="col-12" wire:ignore x-data="{
                        visible: true,
                        selectedDistrict: $wire.entangle('selectedDistrict').live,
                        selectedCrop: $wire.entangle('selectedCrop'),
                        selectedSeason: $wire.entangle('selectedSeason'),
                        selectedTypeOfProduce: $wire.entangle('selectedTypeOfProduce'),
                        selectedEPA: $wire.entangle('selectedEPA'),
                        selectedSection: $wire.entangle('selectedSection'),
                        selectedSex: $wire.entangle('selectedSex'),
                        districts: $wire.entangle('districts'),
                        crops: $wire.entangle('crops'),
                        seasons: $wire.entangle('seasons'),
                        typeOfProduces: $wire.entangle('typeOfProduces'),
                        epas: $wire.entangle('epas'),
                        sections: $wire.entangle('sections'),
                        genders: $wire.entangle('genders'),

                        filterData() {
                            this.visible = false;
                            $wire.dispatch('updateReport', {
                                crop: this.selectedCrop,
                                district: this.selectedDistrict,
                                season: this.selectedSeason,
                                typeOfProduce: this.selectedTypeOfProduce,
                                epa: this.selectedEPA,
                                section: this.selectedSection,
                                gender: this.selectedSex,



                            });

                            setTimeout(() => { this.visible = true }, 1500)

                        },
                        resetFilter() {
                            this.visible = false;
                            $wire.dispatch('resetReport');
                            setTimeout(() => { this.visible = true }, 1500)
                        }
                    }">

                        <!-- Filter Card -->
                        <div class="border-0 shadow-sm card rounded-4"
                            :class="{ 'opacity-50 pe-none': visible === false }">

                            <!-- Header -->
                            <div class="border-0 card-header bg-light rounded-top-4 d-flex align-items-center">
                                <i class="bx bx-filter fs-5 me-2 text-warning"></i>
                                <span class="fw-semibold text-dark">Filters</span>
                            </div>

                            <!-- Body -->
                            <div class="card-body">
                                <div class="row g-3">

                                    <!-- District -->
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-semibold small text-muted">District</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i class="bx bx-map"></i></span>
                                            <select x-model="selectedDistrict" class="form-select">
                                                <option value="">All Districts</option>
                                                <template x-for="district in districts" :key="district">
                                                    <option x-text="district" :value="district"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Crop -->
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-semibold small text-muted">Crop</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i
                                                    class="fa fa-seedling"></i></span>
                                            <select x-model="selectedCrop" class="form-select">
                                                <option value="">All Crops</option>
                                                <template x-for="crop in crops" :key="crop">
                                                    <option x-text="crop" :value="crop"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Season -->
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-semibold small text-muted">Season</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i
                                                    class="bx bx-calendar"></i></span>
                                            <select x-model="selectedSeason" class="form-select">
                                                <option value="">All Seasons</option>
                                                <template x-for="season in seasons" :key="season">
                                                    <option x-text="season" :value="season"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Type of Produce -->
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-semibold small text-muted">Type of Produce</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i
                                                    class="bx bx-food-menu"></i></span>
                                            <select x-model="selectedTypeOfProduce" class="form-select">
                                                <option value="">All Types</option>
                                                <template x-for="produce in typeOfProduces" :key="produce">
                                                    <option x-text="produce" :value="produce"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- EPA -->
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-semibold small text-muted">EPA</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i
                                                    class="bx bx-buildings"></i></span>
                                            <select x-model="selectedEPA" class="form-select">
                                                <option value="">All EPAs</option>
                                                <template x-for="epa in epas" :key="epa">
                                                    <option x-text="epa" :value="epa"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Section -->
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-semibold small text-muted">Section</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i class="bx bx-grid"></i></span>
                                            <select x-model="selectedSection" class="form-select">
                                                <option value="">All Sections</option>
                                                <template x-for="section in sections" :key="section">
                                                    <option x-text="section" :value="section"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Sex -->
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-semibold small text-muted">Sex</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light"><i class="bx bx-user"></i></span>
                                            <select x-model="selectedSex" class="form-select">
                                                <option value="">All</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Footer / Buttons -->
                            <div
                                class="gap-2 border-0 card-footer bg-light d-flex justify-content-end rounded-bottom-4">
                                <button class="btn btn-sm btn-warning fw-semibold" @click="filterData">
                                    <i class="bx bx-check-circle me-1"></i> Apply
                                </button>
                                <button @click="resetFilter" class="btn btn-sm btn-outline-secondary">
                                    <i class="bx bx-reset me-1"></i> Reset
                                </button>
                            </div>
                        </div>




                    </div>


                </div>
                {{-- <div>
                    <div class="d-flex justify-content-end">
                      </div>
                </div> --}}

            </div>
        </div>

    </div>
    @if (!$showContent)
        <div x-data x-init="() => {
            setTimeout(() => {

                $wire.dispatch('showCharts3');
            }, 5000)
        }">



            @include('placeholders.dashboard3')
        </div>
    @else
        <livewire:charts-view-3 :data="$grossMarginData" :farmingCostData="$farmingCostData" :grossMarginCalculations="$grossMarginCalculations" :grossMarginVarieties="$grossMarginVarieties" :costing="$farmingCostsArray"
            :grossCategories="$grossCategories" :farmingCostVarieties="$farmingCostVarieties" :farmingCostCalculations="$farmingCostCalculations" />
    @endif
</div>
