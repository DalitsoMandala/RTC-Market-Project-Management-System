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


        <div class="row d-none">
            <div class="col">
                <livewire:indicator-targets.view :indicator_id="$indicator_id" :project_id="$project_id" :total="$total" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary" role="alert">
                    <button class="btn btn-primary" @click="downloadForm()"> Download this data</button>
                </div>

            </div>
        </div>
        <div class="row ">
            <div class="col-12 col-md-7">
                <div class="border shadow-none card ">


                    <div class="card-body ">
                        <div class="table-responsive ">
                            <table class="table mb-0 table-hover table-striped table-bordered" id="table1">
                                <thead class="table-primary">
                                    <tr>
                                        <th scope="col">Indicator Number</th>
                                        <th scope="col">Disaggregation</th>
                                        <th scope="col">Value</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($data as $index => $value)
                                        <tr class="">
                                            <td scope="row">{{ $indicator_no }}</td>
                                            <td scope="row">{{ $index }}</td>
                                            <td scope="row">{{ $value }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>



            </div>

            <div class="col-md-5 col-12">
                <div class="border shadow-none card card-body" x-data="{
                    chartData: @js($data),
                    categories: ['Total'],
                    values: [],
                    init() {
                        let data = this.chartData;
                        categories = Object.keys(data); // ['Cassava', 'Potato', 'Sweet potato', 'Total']
                        seriesData = Object.values(data); // [0, 0, 0, 0]
                
                
                
                        options = {
                            chart: {
                                type: 'bar'
                            },
                
                            series: [{
                                name: 'Count',
                                data: seriesData
                            }],
                            colors: ['#006989', '#E88D67', '#FA7070'],
                            xaxis: {
                                categories: categories
                            }
                        };
                
                        let chart = new ApexCharts($refs.chart, options);
                        chart.render();
                    }
                }">
                    <div x-ref="chart"></div>
                </div>


            </div>
        </div>

    </div>
    @assets
        <script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
    @endassets

    @script
        <script></script>
    @endscript
</div>
