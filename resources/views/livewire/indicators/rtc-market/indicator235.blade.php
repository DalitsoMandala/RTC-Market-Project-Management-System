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

                    <div class="text-center col-md-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Crop Distribution</h5>

                                </h5>
                            </div>

                            <div class="card-body">
                                <div id="cropChart" x-show='!hasZeroValues(cropChart)'></div>

                                <x-no-data x-show='hasZeroValues(cropChart)' />
                            </div>
                        </div>


                    </div>

                    <div class="text-center col-md-6 col-12">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Seed
                                    Distribution</h5>

                            </div>

                            <div class="card-body">
                                <div id="seed" x-show="!hasZeroValues(seed)"></div>

                                <x-no-data x-show='hasZeroValues(seed)' />
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
            genderChart: [],
            ageGroupChart: [],
            professionChart: [],
            cropChart: [],
            farmers: [],
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


                this.cropChart = [data.Cassava, data.Potato, data['Sweet potato']];
                this.seed = [
                    data['Seed'],
                    data['Produce'],

                ];


            },


            init() {

                let data = this.data;
                this.setData(data);





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

                const seed = new ApexCharts(document.querySelector("#seed"), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: ['#DE8F5F', '#FC931D', '#FA7070'],
                    legend: {
                        position: 'bottom'
                    },
                    series: this.seed,
                    labels: ['Seed', 'Produce']
                });
                seed.render();



            },


        }));
    </script>
@endscript
