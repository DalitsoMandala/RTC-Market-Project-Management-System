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
                    <div class="text-center col-md-4 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Markets</h5>

                            </div>

                            <div class="card-body">
                                <div id="markets"></div>
                            </div>
                        </div>

                    </div>
                    <div class="text-center col-md-4 col-12">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Imports & Exports Distribution</h5>


                            </div>

                            <div class="card-body">
                                <div id="totalCropBar" x-show='!hasZeroValues(totalCropBar)'></div>
                                <x-no-data x-show='hasZeroValues(totalCropBar)' />
                            </div>
                        </div>
                    </div>



                    <div class="text-center col-md-4 col-12">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Product Distribution</h5>


                            </div>

                            <div class="card-body">
                                <div id="pos" x-show='!hasZeroValues(pos)'></div>
                                <x-no-data x-show='hasZeroValues(pos)' />
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
            markets: [],
            totalCropBar: [],
            seed: [],
            pos: [],
            volume: [],

            data: $wire.entangle('data'),



            hasZeroValues(array) {
                if (!array.every(item => typeof item === 'number')) {

                    throw new Error("Array contains non-number elements.");
                }


                return array.reduce((a, b) => a + b, 0) === 0;
            },


            setData(data) {


                this.markets = [
                    data['Domestic markets'],
                    data['International markets'],
                ];

                this.totalCropBar = [
                    data.Imports,
                    data.Exports,

                ]
                this.pos = [
                    data['Seed'],
                    data['Produce'],
                    data['Value added products'],
                ];



                this.seed = [
                    data['Basic'],
                    data['Certified']
                ]




            },

            init() {

                let data = this.data;
                this.setData(data);
                const markets = new ApexCharts(document.querySelector("#markets"), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    colors: ['#DE8F5F', '#FC931D', '#FA7070'],
                    legend: {
                        position: 'bottom'
                    },
                    series: this.markets,
                    labels: ['Domestic markets', 'International markets']
                });
                markets.render();



                const totalCropBar = new ApexCharts(document.querySelector(
                    "#totalCropBar"), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },


                    series: this.totalCropBar,

                    categories: ['Imports', 'Exports'],

                    legend: {
                        position: 'bottom'
                    },
                    labels: ['Imports', 'Exports'],

                    colors: ['#FC931D', '#FA7070', '#DE8F5F'], // Bar and line colors
                })

                totalCropBar.render();



                const seedChart = new ApexCharts(document.querySelector("#seed"), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: ['#DE8F5F', '#FC931D', '#FA7070'],
                    legend: {
                        position: 'bottom'
                    },
                    series: this.seed,
                    labels: ['Basic', 'Certified']
                });
                seedChart.render();




                const poChart = new ApexCharts(document.querySelector("#pos"), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: ['#DE8F5F', '#FC931D', '#FA7070', '#eb5a3c'],
                    legend: {
                        position: 'bottom'
                    },
                    series: this.pos,
                    labels: ['Seed', 'Produce', 'Value added products']
                });
                poChart.render();





            },


        }));
    </script>
@endscript
