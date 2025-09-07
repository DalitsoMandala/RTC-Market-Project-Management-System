<div>

    <div x-data="dashboard()" class="my-4">
        <!-- Stats Cards -->


        <div class="row g-3">
            <!-- Total Valuable Costs (MWK) -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="border-0 shadow-sm card rounded-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Total Valuable Costs</div>
                            <div class="mt-1 fs-4 fw-bold">
                                {{ number_format($grossMarginCalculations['total_valuable_cost'] ?? 0, 2) }}
                                <span class="badge bg-light text-dark ms-2">MWK</span>
                            </div>
                        </div>
                        <div class="display-6 text-warning"><i class="bx bx-wallet"></i></div>
                    </div>
                </div>
            </div>

            <!-- Total Harvest -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="border-0 shadow-sm card rounded-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Total Harvest</div>
                            <div class="mt-1 fs-4 fw-bold">
                                {{ number_format($grossMarginCalculations['total_harvest'] ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="display-6 text-success"><i class="bx bx-basket"></i></div>
                    </div>
                </div>
            </div>

            <!-- Prevailing Selling Price (MWK) -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="border-0 shadow-sm card rounded-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Prevailing Selling Price</div>
                            <div class="mt-1 fs-4 fw-bold">
                                {{ number_format($grossMarginCalculations['prevailing_selling_price'] ?? 0, 2) }}
                                <span class="badge bg-light text-dark ms-2">MWK</span>
                            </div>
                        </div>
                        <div class="display-6 text-primary"><i class="bx bx-purchase-tag"></i></div>
                    </div>
                </div>
            </div>

            <!-- Income (MWK) -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="border-0 shadow-sm card rounded-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Income</div>
                            <div class="mt-1 fs-4 fw-bold">
                                {{ number_format($grossMarginCalculations['income'] ?? 0, 2) }}
                                <span class="badge bg-light text-dark ms-2">MWK</span>
                            </div>
                        </div>
                        <div class="display-6 text-info"><i class="bx bx-line-chart"></i></div>
                    </div>
                </div>
            </div>

            <!-- Yield (kg/unit) -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="border-0 shadow-sm card rounded-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Yield</div>
                            <div class="mt-1 fs-4 fw-bold">
                                {{ number_format($grossMarginCalculations['yield'] ?? 0, 2) }}
                                <span class="badge bg-light text-dark ms-2">kg/unit</span>
                            </div>
                        </div>
                        <div class="display-6 text-secondary"><i class="bx bx-scatter-chart"></i></div>
                    </div>
                </div>
            </div>

            <!-- Break-even Yield -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="border-0 shadow-sm card rounded-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Break-even Yield</div>
                            <div class="mt-1 fs-4 fw-bold">
                                {{ number_format($grossMarginCalculations['break_even_yield'] ?? 0, 2) }}
                                <span class="badge bg-light text-dark ms-2">kg/unit</span>
                            </div>
                        </div>
                        <div class="display-6 text-danger"><i class="bx bx-trending-down"></i></div>
                    </div>
                </div>
            </div>

            <!-- Break-even Price (MWK) -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="border-0 shadow-sm card rounded-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Break-even Price</div>
                            <div class="mt-1 fs-4 fw-bold">
                                {{ number_format($grossMarginCalculations['break_even_price'] ?? 0, 2) }}
                                <span class="badge bg-light text-dark ms-2">MWK</span>
                            </div>
                        </div>
                        <div class="display-6 text-dark"><i class="bx bx-coin"></i></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="my-4 border-0 shadow-sm card rounded-2">
                    <div class="mt-2 border-0 card-header align-items-center d-flex justify-content-between">
                        <span class="fw-semibold text-dark">Gross Margin</span>
                        <button class=" btn btn-warning btn-sm" @click="downloadTable('grossMargin')">Download Excel</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rounded-2">
                            <table class="table table-bordered " id="grossMargin">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Item</th>
                                        <th>Unit (Mulingo)</th>
                                        <th>AVG - QTY (Kuchuluka)</th>
                                        <th>AVG - Unit Price (Mtengo wa chimodzi)</th>
                                        <th>AVG - Total (Zonse pamodzi)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($grossCategories as $category)
                                        <tr class="table-secondary">
                                            <td colspan="5"><strong>{{ $category['name'] }}</strong></td>
                                        </tr>
                                        @if ($category['name'] == 'Seed (Mbeu/Variety)')
                                            @foreach ($grossMarginVarieties as $grossMarginVariety)
                                                <tr>
                                                    <td>{{ $grossMarginVariety['variety'] }}</td>
                                                    <td>{{ $grossMarginVariety['unit'] }}</td>
                                                    <td>{{ $grossMarginVariety['avg_qty'] }}</td>
                                                    <td> {{ $grossMarginVariety['avg_unit_price'] }}</td>
                                                    <td> {{ $grossMarginVariety['avg_total'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @foreach ($data as $grossData)
                                            @if ($grossData['category'] == $category['name'])
                                                <tr>
                                                    <td>{{ $grossData['item_name'] }}</td>
                                                    <td>{{ $grossData['unit'] }}</td>
                                                    <td>{{ $grossData['avg_qty'] }}</td>
                                                    <td> {{ $grossData['avg_unit_price'] }}</td>
                                                    <td> {{ $grossData['avg_total'] }}</td>

                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach



                                </tbody>

                                <tfoot class="table-secondary ">
                                    <tr>
                                        <td><strong>Total Valuable Costs (MWK):</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $grossMarginCalculations['total_valuable_cost'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Total Harvest:</strong>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $grossMarginCalculations['total_harvest'] }}</td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <strong>Prevailing Selling Price (MWK):</strong>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $grossMarginCalculations['prevailing_selling_price'] }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Income (MWK):</strong></td>
                                        <!-- Description -->
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $grossMarginCalculations['income'] }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Yield (kg/unit):</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $grossMarginCalculations['yield'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Break-even Yield:</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $grossMarginCalculations['break_even_yield'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Break-even Price (MWK):</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $grossMarginCalculations['break_even_price'] }}</td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <td colspan="4" class="text-end"><strong class="fs-5">Gross Margin
                                                (Profit) MWK:</strong></td>
                                        <td>{{ $grossMarginCalculations['gross_margin'] }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="my-4 border-0 shadow-sm card rounded-2">
                    <div class="mt-2 border-0 rounded-2 card-header align-items-center d-flex justify-content-between">
                        <span class="fw-semibold text-dark">Farming Cost</span>
                        <button class=" btn btn-warning btn-sm" @click="downloadTable('farmingCost')">Download Excel</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rounded-2">
                            <table class="table table-bordered " id="farmingCost">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Item</th>
                                        <th> Unit (Mulingo)</th>
                                        <th>QTY (Kuchuluka)</th>
                                        <th>Unit Price (Mtengo wa chimodzi)</th>
                                        <th>Total (Zonse pamodzi)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($grossCategories as $category)
                                        <tr class="table-secondary">
                                            <td colspan="5"><strong>{{ $category['name'] }}</strong></td>
                                        </tr>
                                        @if ($category['name'] == 'Seed (Mbeu/Variety)')
                                            @foreach ($farmingCostVarieties as $grossMarginVariety)
                                                <tr>
                                                    <td>{{ $grossMarginVariety['variety'] }}</td>
                                                    <td>{{ $grossMarginVariety['unit'] }}</td>
                                                    <td>1</td>
                                                    <td> {{ $grossMarginVariety['avg_unit_price'] }}</td>
                                                    <td> {{ $grossMarginVariety['avg_unit_price'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @foreach ($farmingCostData as $farmData)
                                            @if ($farmData['category'] == $category['name'])
                                                <tr>
                                                    <td>{{ $farmData['item_name'] }}</td>
                                                    <td>{{ $farmData['unit'] }}</td>
                                                    <td>1</td>
                                                    <td> {{ $farmData['avg_unit_price'] }}</td>
                                                    <td> {{ $farmData['avg_unit_price'] }}</td>

                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach
                                <tfoot>
                                    <tr class="table-secondary">
                                        <td colspan="4" class="text-end"><strong class="fs-5">Total Farming Cost
                                                (MWK):</strong></td>
                                        <td>{{ $farmingCostCalculations['unit_price_total'] }}</td>
                                    </tr>
                                </tfoot>


                                </tbody>


                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush
@script
    <script>
        Alpine.data('dashboard', () => ({
            filters: {
                district: '',
                enterprise: '',
                season: ''
            },
            data: @js($data),


            filteredData() {
                return this.data.filter(e =>
                    (!this.filters.district || e.district === this.filters.district) &&
                    (!this.filters.enterprise || e.enterprise === this.filters.enterprise) &&
                    (!this.filters.season || e.season === this.filters.season)
                );
            },
            avg(field) {
                let items = this.filteredData();

                if (items.length === 0) return 0;
                return (items.reduce((sum, e) => sum + Number(e[field]), 0) / items.length).toFixed(2);
            },
            uniqueValues(field) {
                return [...new Set(this.data.map(e => e[field]))];
            },

            downloadTable(id) {
                // Get the table element
                let table = document.querySelector("#" + id);

                // Convert table to SheetJS worksheet
                let wb = XLSX.utils.table_to_book(table, {
                    sheet: id
                });

                // Export to Excel file
                XLSX.writeFile(wb, id + ".xlsx");
            }
        }));
    </script>
@endscript
