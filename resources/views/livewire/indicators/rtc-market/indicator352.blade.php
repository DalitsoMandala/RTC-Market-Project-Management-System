<div>

    <div>





        <div class="row">
             <div class="col-12 col-md-12 ">
<x-table-card title="Table view" subtitle="Indicator A1 data" :isResponsive="false">
                    <div>
                        <livewire:tables.indicator-detail-table :populatedData="$data" :name="$indicator_name" />
                    </div>
                </x-table-card>
</div>



        </div>

    </div>


</div>
@script
    <script>
        Alpine.data('dashboard', () => ({
            rtcPeople: [],


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


                this.rtcPeople = [data['RTC actors and households'],
                    data['School feeding beneficiaries']
                ];




            },


            init() {

                let data = this.data;
                this.setData(data);


                const rtcChart = new ApexCharts(document.querySelector("#cropChart"), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    colors: ['#DE8F5F', '#FC931D', '#FA7070'],
                    legend: {
                        position: 'bottom'
                    },
                    series: this.rtcPeople,
                    labels: ['RTC actors and households', 'School feeding beneficiaries']
                });
                rtcChart.render();


            },


        }));
    </script>
@endscript
