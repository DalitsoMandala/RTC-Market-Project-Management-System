<div>

    <div x-data="{
    
        downloadForm() {
            // Create a new workbook
            var wb = XLSX.utils.book_new();
    
            // List of table IDs
            var tableIds = ['table1', 'table2', 'table3', 'table4', 'table5', 'table6']; // Add more table IDs as needed
    
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
            <div class="col-12 col-md-7">
                <div class="border shadow-none card ">


                    <div class="card-body ">
                        <div class="table-responsive text-uppercase">
                            <table class="table table-hover table-striped table-bordered" id="table1">
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
                    categories: ['Farmers', 'Processors', 'Traders'],
                    values: [],
                    init() {
                        let data = this.chartData;
                        this.values = [data['Farmers'], data['Processors'], data['Traders']];
                        options = {
                            chart: {
                                type: 'pie',
                                width: 400
                            },
                            labels: this.categories,
                            series: this.values,
                            colors: ['#006989', '#E88D67', '#FA7070'],
                            legend: {
                                position: 'top'
                            }
                        }
                
                        let chart = new ApexCharts($refs.chart, options);
                        chart.render();
                    }
                }">
                    <div x-ref="chart"></div>
                </div>

                <div class="border shadow-none card card-body" x-data="{
                    chartData: @js($data),
                    categories: ['Cassava', 'Potato', 'Sweet potato'],
                    values: [],
                    init() {
                        let data = this.chartData;
                        this.values = [data['Cassava'], data['Potato'], data['Sweet potato']];
                        options = {
                            chart: {
                                type: 'donut',
                                width: 425
                            },
                            labels: this.categories,
                            series: this.values,
                            colors: ['#006989', '#E88D67', '#FA7070'],
                            legend: {
                                position: 'right'
                            }
                        }
                
                        let chart = new ApexCharts($refs.chart, options);
                        chart.render();
                    }
                }">
                    <div x-ref="chart"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="border shadow-none card">
                    <div class="card-header">
                        <div class="card-body">
                            <div class="table-responsive text-uppercase">
                                <table class="table table-hover table-striped table-bordered" id="table2">
                                    <thead class="table-primary">
                                        <tr>

                                            <th colspan="3">
                                                Rtc Actor By Sex - {{ $indicator_no }}
                                            </th>
                                        </tr>
                                        <tr>

                                            <th scope="col">Disaggregation</th>
                                            <th scope="col">Males</th>
                                            <th scope="col">Females</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($dataByActorMales as $index => $value)
                                            <tr class="">

                                                <td scope="row">
                                                    {{ $index === 'sweet_potato' ? str_replace('_', ' ', $index) : $index }}
                                                </td>
                                                <td>{{ $dataByActorMales[$index] }}</td>
                                                <td>{{ $dataByActorFemales[$index] }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="border shadow-none card">
                    <div class="card-header">
                        <div class="card-body" x-data="{
                            chartData: [],
                            categories: ['Cassava', 'Potato', 'Sweet potato'],
                            values: [],
                            init() {
                                let data = @js($dataBySex);
                                // Convert data object into a format suitable for ApexCharts
                                const chartData = [
                                    { name: 'Farmers', male: data.males.Farmers, female: data.females.Farmers },
                                    { name: 'Processors', male: data.males.Processors, female: data.females.Processors },
                                    { name: 'Traders', male: data.males.Traders, female: data.females.Traders },
                                ];
                                const options = {
                                    chart: {
                                        type: 'bar', // Choose bar chart for this data
                                        stacked: false, // Stack bars to show male and female side-by-side
                                    },
                        
                                    xaxis: {
                                        categories: chartData.map(item => item.name), // Use category labels from data
                                        title: {
                                            text: 'Disaggregation', // Optional X-axis title
                                        },
                                    },
                                    yaxis: {
                                        title: {
                                            text: 'Number of People', // Optional Y-axis title
                                        },
                                    },
                                    series: [{
                                            name: 'Males',
                                            data: chartData.map(item => item.male), // Extract male data
                                        },
                                        {
                                            name: 'Females',
                                            data: chartData.map(item => item.female), // Extract female data
                                        },
                                    ],
                                    fill: {
                                        opacity: 1, // Set fill opacity for better visibility
                                    },
                                    legend: {
                                        position: 'top', // Optional legend position
                                    },
                                    colors: ['#006989', '#E88D67', '#FA7070']
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

        </div>
        <div class="row">
            <div class="col">
                <div class="border shadow-none card">
                    <div class="card-header">
                        <div class="card-body">
                            <div class="table-responsive text-uppercase">
                                <table class="table table-hover table-striped table-bordered" id="table3">
                                    <thead class="table-primary">
                                        <tr>

                                            <th colspan="2">
                                                Rtc Actor By Crop [Farmers] - {{ $indicator_no }}
                                            </th>
                                        </tr>
                                        <tr>

                                            <th scope="col">Disaggregation</th>
                                            <th scope="col">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($dataByCrop['Farmers'] as $index => $value)
                                            <tr class="">

                                                <td scope="row">
                                                    {{ $index === 'sweet_potato' ? str_replace('_', ' ', $index) : $index }}
                                                </td>
                                                <td scope="row">{{ (int) $value }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="border shadow-none card">
                    <div class="card-header">
                        <div class="card-body">
                            <table class="table table-hover table-striped table-bordered text-uppercase" id="table4">
                                <thead class="table-primary">
                                    <tr>

                                        <th colspan="2">
                                            Rtc Actor By Crop [Processors] - {{ $indicator_no }}
                                        </th>
                                    </tr>
                                    <tr>

                                        <th scope="col">Disaggregation</th>
                                        <th scope="col">Value</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($dataByCrop['Processors'] as $index => $value)
                                        <tr class="">

                                            <td scope="row">
                                                {{ $index === 'sweet_potato' ? str_replace('_', ' ', $index) : $index }}
                                            </td>
                                            <td scope="row">{{ (int) $value }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="border shadow-none card">
                    <div class="card-header">
                        <div class="card-body">
                            <div class="table-responsive text-uppercase">
                                <table class="table table-hover table-striped table-bordered" id="table5">
                                    <thead class="table-primary">
                                        <tr>

                                            <th colspan="2">
                                                Rtc Actor By Crop [Traders] - {{ $indicator_no }}
                                            </th>
                                        </tr>
                                        <tr>

                                            <th scope="col">Disaggregation</th>
                                            <th scope="col">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($dataByCrop['Traders'] as $index => $value)
                                            <tr class="">

                                                <td scope="row">
                                                    {{ $index === 'sweet_potato' ? str_replace('_', ' ', $index) : $index }}
                                                </td>
                                                <td scope="row">{{ (int) $value }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="border shadow-none card">
                    <div class="card-header">
                        <div class="card-body" x-data="{
                            chartData: [],
                            categories: ['Cassava', 'Potato', 'Sweet potato'],
                            values: [],
                            init() {
                                let data = @js($dataByAge);
                                console.log(data);
                                const chartData = [
                                    { name: 'Farmers', male: data.youth.Farmers, female: data.not_youth.Farmers },
                                    { name: 'Processors', male: data.youth.Processors, female: data.not_youth.Processors },
                                    { name: 'Traders', male: data.youth.Traders, female: data.not_youth.Traders },
                                ];
                                const options = {
                                    chart: {
                                        type: 'bar', // Choose bar chart for this data
                                        stacked: false, // Stack bars to show male and female side-by-side
                                    },
                        
                                    xaxis: {
                                        categories: chartData.map(item => item.name), // Use category labels from data
                                        title: {
                                            text: 'Disaggregation', // Optional X-axis title
                                        },
                                    },
                                    yaxis: {
                                        title: {
                                            text: 'Number of People', // Optional Y-axis title
                                        },
                                    },
                                    series: [{
                                            name: 'Youth',
                                            data: chartData.map(item => item.male), // Extract male data
                                        },
                                        {
                                            name: 'Not Youth',
                                            data: chartData.map(item => item.female), // Extract female data
                                        },
                                    ],
                                    fill: {
                                        opacity: 1, // Set fill opacity for better visibility
                                    },
                                    legend: {
                                        position: 'top', // Optional legend position
                                    },
                                    colors: ['#006989', '#E88D67', '#FA7070']
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
            <div class="col">
                <div class="border shadow-none card">
                    <div class="card-header">
                        <div class="card-body">
                            <div class="table-responsive text-uppercase">
                                <table class="table table-hover table-striped table-bordered" id="table6">
                                    <thead class="table-primary">
                                        <tr>

                                            <th colspan="3">
                                                Rtc Actor By Age group - {{ $indicator_no }}
                                            </th>
                                        </tr>
                                        <tr>

                                            <th scope="col">Disaggregation</th>
                                            <th scope="col">Youth(18-35)</th>
                                            <th scope="col">Not Youth (+35)</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($dataByActorYouth as $index => $value)
                                            <tr class="">

                                                <td scope="row">
                                                    {{ $index === 'sweet_potato' ? str_replace('_', ' ', $index) : $index }}
                                                </td>
                                                <td>{{ $dataByActorYouth[$index] }}</td>
                                                <td>{{ $dataByActorNotYouth[$index] }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
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
