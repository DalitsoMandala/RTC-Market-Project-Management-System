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
                    <div class="text-center col-md-3 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Percentage</h5>

                            </div>

                            <div class="card-body">
                                <div id="totalCrop"></div>

                            </div>
                        </div>

                    </div>
                    <div class="text-center  col-md-9 col-12">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Crop Distribution</h5>


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
                    data.Potato,
                    data.Cassava,
                    data['Sweet potato'],
                ];


            },

            init() {

                let data = this.data;
                this.setData(data);

                const totalChart = new ApexCharts(document.querySelector("#totalCrop"), {
                    series: this.totalCrop,
                    chart: {
                        height: 350,
                        type: 'radialBar',
                    },

                    colors: ['#eb5a3c'],
                    labels: ['Total (%)'],
                    stroke: {
                        lineCap: 'round'
                    },

                    plotOptions: {

                        radialBar: {
                            hollow: {
                                size: '70%',
                            },
                            dataLabels: {
                                total: {
                                    show: false,

                                    formatter: function(val) {
                                        return val + '%'; // Highlight the highest or key value
                                    }
                                }
                            }
                        }
                    }
                })

                totalChart.render();



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


                    labels: ['Potato', 'Sweet potato', 'Cassava'], // Categories
                    colors: ['#FC931D', '#FA7070', '#DE8F5F'], // Bar and line colors
                    xaxis: {
                        categories: ['Potato', 'Sweet potato', 'Cassava'], // X-axis labels

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
