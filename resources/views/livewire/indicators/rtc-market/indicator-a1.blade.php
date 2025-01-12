<div>

    <div x-data="{
    
        downloadForm() {
            // Create a new workbook
            var wb = XLSX.utils.book_new();
    
            // List of table IDs
            var tableIds = ['table1']; // Add more table IDs as needed
    
            // Loop through each table ID
            tableIds.forEach(function(id, index) {
                // Get the table element
                var table = document.getElementById(id);
                if (table) {
                    // Convert the table to a sheet
                    var ws = XLSX.utils.table_to_sheet(table);
                    // Add the sheet to the workbook
                    XLSX.utils.book_append_sheet(wb, ws, 'Sheet' + (index + 1));
                }
            });
    
            // Write the workbook to a file
            XLSX.writeFile(wb, '{{ $indicator_name }}_{{ $indicator_no }}.xlsx');
        }
    }">





        <div class="row gy-1 ">
            <div class="col-12 col-md-12">
                <h5 class="p-2 mb-3 text-center card-title fw-bold text-warning bg-soft-warning rounded-1"> <i
                        class="bx bx-table"></i> Table
                    view
                </h5>
                <livewire:tables.indicator-detail-table :populatedData="$data" :name="$indicator_name" />
            </div>
            <div class="col-12 col-md-12 ">
                <h5 class="p-2 mb-3 text-center card-title fw-bold text-warning bg-soft-warning rounded-1"> <i
                        class="bx bx-table"></i> Chart view</h5>
                <div class="row gy-3" x-data="dashboard">
                    <div class="text-center col-6">
                        <h3 class=" text-muted h5">Crop Distribution</h3>
                        <span class="bx bx-chevron-down text-muted fw-bold" style="font-size:15px"></span>
                        <div id="cropChart" x-show='!hasZeroValues(cropChart)'></div>

                        <x-no-data x-show='hasZeroValues(cropChart)' />
                    </div>

                    <div class="text-center col-6">
                        <h3 class=" text-muted h5">Age Group
                            Distribution</h3> <span class="bx bx-chevron-down text-muted fw-bold"
                            style="font-size:15px"></span>
                        <div id="ageGroupChart" x-show="!hasZeroValues(ageGroupChart)"></div>

                        <x-no-data x-show='hasZeroValues(ageGroupChart)' />
                    </div>
                    <div class="text-center col-6">
                        <h3 class=" text-muted h5">Establishment Distribution</h3>
                        <span class="bx bx-chevron-down text-muted fw-bold" style="font-size:15px"></span>
                        <div id="establishmentChart" x-show="!hasZeroValues(establishmentChart)"></div>
                        <x-no-data x-show="hasZeroValues(establishmentChart)" />
                    </div>
                    <div class="text-center col-6">
                        <h3 class=" text-muted h5">Gender Distribution</h3>
                        <span class="bx bx-chevron-down text-muted fw-bold" style="font-size:15px"></span>
                        <div id="genderChart" x-show="!hasZeroValues(genderChart)"></div>
                        <x-no-data x-show="hasZeroValues(genderChart)" />
                    </div>
                    <div class="text-center col-6">
                        <h3 class=" text-muted h5">Actor Distribution</h3>
                        <span class="bx bx-chevron-down text-muted fw-bold" style="font-size:15px"></span>
                        <div id="professionChart" x-show="!hasZeroValues(professionChart)"></div>
                        <x-no-data x-show="hasZeroValues(professionChart)" />
                    </div>

                </div>

            </div>


        </div>

    </div>


</div>
@script
    <script>
        Alpine.data('dashboard', () => ({
            genderChart: [],
            ageGroupChart: [],
            professionChart: [],
            cropChart: [],
            establishmentChart: [],
            data: $wire.entangle('data'),

            changeYear(data) {
                $wire.dispatch('updateReportYear', {
                    id: data.id,
                });
            },

            hasZeroValues(array) {
                if (!array.every(item => typeof item === 'number')) {
                    throw new Error("Array contains non-number elements.");
                }
                return array.reduce((a, b) => a + b, 0) === 0;
            },
            setData(data) {


                this.genderChart = [data.Female, data.Male];
                this.ageGroupChart = [data['Youth (18-35 yrs)'], data['Not youth (35yrs+)']];
                this.professionChart = [data.Farmers, data.Processors, data.Traders];
                this.cropChart = [data.Cassava, data.Potato, data['Sweet potato']];
                this.establishmentChart = [
                    data['Employees on RTC establishment'],
                    data['New establishment'],
                    data['Old establishment']
                ];


            },


            init() {

                let data = this.data;
                this.setData(data);

                // Initialize charts and store instances
                const genderChartInstance = new ApexCharts(document.querySelector("#genderChart"), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: ['#FC931D', '#FA7070', '#DE8F5F'],
                    legend: {
                        position: 'bottom'
                    },
                    series: this.genderChart,
                    labels: ['Female', 'Male']
                });
                genderChartInstance.render();

                const ageGroupChartInstance = new ApexCharts(document.querySelector("#ageGroupChart"), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    },
                    colors: ['#FC931D', '#FA7070', '#DE8F5F'],
                    series: this.ageGroupChart,
                    labels: ['Youth (18-35 yrs)', 'Not Youth (35yrs+)']
                });
                ageGroupChartInstance.render();

                const professionChartInstance = new ApexCharts(document.querySelector("#professionChart"), {
                    chart: {
                        type: 'bar',
                        height: 213,
                        toolbar: {
                            show: false, // Disables the entire toolbar including the download button
                        },
                    },
                    series: [{
                        data: this.professionChart
                    }],
                    colors: ['#FC931D', '#FA7070', '#DE8F5F'],
                    xaxis: {
                        categories: ['Farmers', 'Processors', 'Traders']
                    },
                });
                professionChartInstance.render();
                const cropChartInstance = new ApexCharts(document.querySelector("#cropChart"), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: ['#DE8F5F', '#FC931D', '#FA7070'],
                    legend: {
                        position: 'bottom'
                    },
                    series: this.cropChart,
                    labels: ['Cassava', 'Potato', 'Sweet Potato']
                });
                cropChartInstance.render();

                const establishmentChartInstance = new ApexCharts(document.querySelector(
                    "#establishmentChart"), {
                    chart: {
                        type: 'bar',
                        height: 285,
                        toolbar: {
                            show: false, // Disables the entire toolbar including the download button
                        },
                    },
                    colors: ['#FC931D', '#FA7070', '#DE8F5F'],
                    series: [{
                        data: this.establishmentChart
                    }],
                    xaxis: {
                        categories: ['Employees on RTC', 'New Establishment', 'Old Establishment']
                    },
                });
                establishmentChartInstance.render();

            },


        }));
    </script>
@endscript
