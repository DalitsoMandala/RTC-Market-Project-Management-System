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




        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary" role="alert">
                    <button class="btn btn-primary" @click="downloadForm()"> Download this data</button>
                </div>

            </div>
        </div>
        <div class="row ">
            <div class="col-12 col">
                <div class="border shadow-none card ">


                    <div class="card-body ">
                        <div class="table-responsive ">
                            <table class="table mb-0 table-hover table-striped table-bordered" id="table1">
                                <thead class="table-primary">
                                    <tr>

                                        <th scope="col">Disaggregation</th>
                                        <th scope="col">Value</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($data as $index => $value)
                                        <tr class="">

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

            <div class="col-12">
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

            <div class="col">
                <div class="border shadow-none card card-body" x-data="{
                    chartData: @js($data),
                    init() {
                        let data = this.chartData;
                        let keysToKeep = ['Male', 'Female'];
                
                        // Filtering the object
                        let filteredData = Object.fromEntries(
                            Object.entries(data).filter(([key]) => keysToKeep.includes(key))
                        );
                
                
                        let categories = Object.keys(filteredData); // ['Cassava', 'Potato', 'Sweet potato', 'Total']
                        let seriesData = Object.values(filteredData); // [0, 0, 0, 0]
                
                        let options = {
                            chart: {
                                type: 'donut'
                            },
                            series: seriesData,
                            labels: categories,
                            colors: ['#006989', '#E88D67', '#FA7070', '#A1C181']
                        };
                
                        let chart = new ApexCharts($refs.genderChart, options);
                        chart.render();
                    }
                }">
                    <div x-ref="genderChart"></div>
                </div>
            </div>

            <div class="col">
                <div class="border shadow-none card card-body" x-data="{
                    chartData: @js($data),
                    init() {
                        let data = this.chartData;
                        let keysToKeep = ['Cassava', 'Potato', 'Sweet potato'];
                
                        // Filtering the object
                        let filteredData = Object.fromEntries(
                            Object.entries(data).filter(([key]) => keysToKeep.includes(key))
                        );
                
                
                        let categories = Object.keys(filteredData); // ['Cassava', 'Potato', 'Sweet potato', 'Total']
                        let seriesData = Object.values(filteredData); // [0, 0, 0, 0]
                
                        let options = {
                            chart: {
                                type: 'radialBar'
                            },
                            series: seriesData,
                            labels: categories,
                            colors: ['#006989', '#E88D67', '#FA7070', '#A1C181']
                        };
                
                        let chart = new ApexCharts($refs.cropChart, options);
                        chart.render();
                    }
                }">
                    <div x-ref="cropChart"></div>
                </div>
            </div>

            <div class="col">
                <div class="border shadow-none card card-body" x-data="{
                    chartData: @js($data),
                    init() {
                        let data = this.chartData;
                        let keysToKeep = ['Farmers', 'Traders', 'Processors'];
                
                        // Filtering the object
                        let filteredData = Object.fromEntries(
                            Object.entries(data).filter(([key]) => keysToKeep.includes(key))
                        );
                
                
                        let categories = Object.keys(filteredData); // ['Cassava', 'Potato', 'Sweet potato', 'Total']
                        let seriesData = Object.values(filteredData); // [0, 0, 0, 0]
                
                        let options = {
                            chart: {
                                type: 'pie'
                            },
                            series: seriesData,
                            labels: categories,
                            colors: ['#006989', '#E88D67', '#FA7070', '#A1C181']
                        };
                
                        let chart = new ApexCharts($refs.cropChart, options);
                        chart.render();
                    }
                }">
                    <div x-ref="cropChart"></div>
                </div>
            </div>
        </div>

    </div>


    @script
        <script></script>
    @endscript
</div>
