<div>

    <div>





        <div class="row gy-1 ">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bx bx-table"></i> Table
                            view
                        </h5>

                    </div>
                    <div class="card-body">
                        <livewire:tables.indicator-detail-table :populatedData="$data" :name="$indicator_name" />
                    </div>
                </div>

            </div>
            <div class="col-12 col-md-12 ">
                <div class="alert alert-warning" role="alert">
                    <strong> <i class="bx bx-chart"></i> Charts</strong>
                </div>

                <div class="row gy-3 justify-content-center" x-data="dashboard">

                    <div class="text-center col-md-12 col-12">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Actor Distribution</h5>


                            </div>

                            <div class="card-body">
                                <div id="totalCropBar" x-show='!hasZeroValues(totalCropBar)'></div>
                                <x-no-data x-show='hasZeroValues(totalCropBar)' />
                            </div>
                        </div>
                    </div>



                </div>


            </div>

        </div>


    </div>

</div>


</div>
@script
    <script>
        Alpine.data('dashboard', () => ({
            totalCrop: [],

            totalCropBar: [],
            data: $wire.entangle('data'),



            hasZeroValues(array) {
                if (!array.every(item => typeof item === 'number')) {
                    throw new Error("Array contains non-number elements.");
                }
                return array.reduce((a, b) => a + b, 0) === 0;
            },
            setData(data) {

                this.totalCrop = [data['Total (% Percentage)']];

                this.totalCropBar = [
                    data.Processors,
                    data.Farmers,
                    data['Large scale processors'],
                    data.SME,
                    data.Loan,
                    data['Input financing']
                ];


            },

            init() {

                let data = this.data;
                this.setData(data);




                const totalCropBarChart = new ApexCharts(document.querySelector("#totalCropBar"), {
                    chart: {
                        height: 338,
                        type: 'bar', // Main chart type
                        toolbar: {
                            show: false, // Disables the entire toolbar including the download button
                        },
                    },
                    series: [{
                            name: 'Value',
                            type: 'bar', // Bar series
                            data: this.totalCropBar, // Raw numeric values for the bar
                        },

                    ],
                    plotOptions: {
                        bar: {
                            distributed: true,
                            endingShape: 'rounded', // Rounded bar ends
                            borderRadius: 4,
                            borderRadiusApplication: 'end',
                        }
                    },


                    labels: ['Processors', 'Farmers', 'Large scale processors', 'SME', 'Loan',
                        'Input financing'
                    ], // Categories
                    colors: ['#FC931D', '#FA7070', '#DE8F5F'], // Bar and line colors
                    xaxis: {
                        categories: ['Processors', 'Farmers', 'Large scale processors', 'SME', 'Loan',
                            'Input financing'
                        ], // X-axis labels

                    },

                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    }


                })

                totalCropBarChart.render();


            },


        }));
    </script>
@endscript
