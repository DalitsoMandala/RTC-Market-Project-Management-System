<div>

    <div>





        <div class="row gy-1 ">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header card-title fw-bold border-bottom-0">
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
                            <div class="card-header card-title fw-bold border-bottom-0">
                                <h5 class="card-title">Percentage</h5>

                            </div>

                            <div class="card-body">
                                <div id="totalCrop"></div>
                            </div>
                        </div>

                    </div>
                    <div class="text-center col-md-9 col-12">

                        <div class="card">
                            <div class="card-header card-title fw-bold border-bottom-0">
                                <h5 class="card-title">Crop Distribution (Formal vs Informal)</h5>


                            </div>

                            <div class="card-body">
                                <div id="totalFormalAndInformalCrops2"
                                    x-show='!(hasZeroValues(formalCrops) && hasZeroValues(informalCrops))'></div>
                                <x-no-data x-show='(hasZeroValues(formalCrops) && hasZeroValues(informalCrops))' />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-12">
                        <div class="card">
                            <div class="card-header card-title fw-bold border-bottom-0">
                                <h5 class="card-title">Processing Status Distribution</h5>


                            </div>

                            <div class="card-body">
                                <div id="statusDistribution" x-show="!hasZeroValues(statusDistribution)"></div>
                                <x-no-data x-show="hasZeroValues(statusDistribution)" />
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
            totalPotato: [],
            totalCassava: [],
            totalSweetPotato: [],
            statusDistribution: [],
            financialValue: [],
            formalCrops: [],
            informalCrops: [],

            data: $wire.entangle('data'),



            hasZeroValues(array) {
                console.log(array)
                if (!array.every(item => typeof item === 'number')) {

                    throw new Error("Array contains non-number elements.");
                }


                return array.reduce((a, b) => a + b, 0) === 0;
            },


            setData(data) {


                this.totalCrop = [data['Total (% Percentage)']];
                this.totalPotato = [data.Potato];
                this.totalCassava = [data.Cassava];
                this.totalSweetPotato = [data['Sweet potato']];
                this.statusDistribution = [data.Raw, data.Processed];
                this.financialValue = [data['Financial value ($)']];
                this.formalCrops = [
                    data['(Formal) Potato'],
                    data['(Formal) Cassava'],
                    data['(Formal) Sweet potato']
                ];
                this.informalCrops = [
                    data['(Informal) Potato'],
                    data['(Informal) Cassava'],
                    data['(Informal) Sweet potato']
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



                const totalFormalAndInformalChart = new ApexCharts(document.querySelector(
                    "#totalFormalAndInformalCrops2"), {
                    chart: {
                        type: 'bar',
                        height: 338
                    },
                    plotOptions: {
                        bar: {

                            borderRadius: 4,
                            endingShape: 'rounded',
                            horizontal: false, // optional, vertical by default
                            columnWidth: '55%' // makes it look cleaner
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    series: [{
                            name: 'Formal',
                            data: this.formalCrops
                        },
                        {
                            name: 'Informal',
                            data: this.informalCrops
                        }
                    ],
                    xaxis: {
                        categories: ['Cassava', 'Potato', 'Sweet Potato']
                    },


                    colors: ['#FC931D', '#FA7070'], // Bar and line colors
                })

                totalFormalAndInformalChart.render();


                const statusDistribution = new ApexCharts(document.querySelector("#statusDistribution"), {
                    chart: {
                        type: 'donut', // Change to 'pie' for a standard pie chart
                        height: 400
                    },
                    series: [data.Raw, data.Processed], // Data for Raw and Processed
                    labels: ['Raw', 'Processed'], // Labels for the data

                    colors: ['#FC931D', '#FA7070', '#DE8F5F'],
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            expandOnClick: true,
                            donut: {
                                size: '65%'
                            }
                        }
                    }
                })

                statusDistribution.render();





            },


        }));
    </script>
@endscript
