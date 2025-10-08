<div>

    <div @update-chart.window="reRenderCharts($event.detail.data)" class="my-4" x-data="dashboard2">


        <!-- Volume vs Value by District (Combo Chart) -->
        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <x-card-header> Estimated Volume (Kg) vs Value (US$) by District</x-card-header>


                    <div class="card-body">
                        <div id="district-combo-chart" x-show="hasDistrictComboData===true" style="height: 500px;"></div>
                        <x-no-data x-show="hasDistrictComboData===false" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">

            <!-- Estimated Demand Chart -->
            <div class="col-md-6">
                <div class="shadow-sm card">
                    <x-card-header>
                        Estimated Demand (Kg) by Variety
                    </x-card-header>
                    <div class="card-body">
                        <div id="demand-chart" x-show="hasDemandData===true" style="height: 350px;"></div>
                        <x-no-data x-show="hasDemandData===false" />
                    </div>
                </div>
            </div>

            <!-- Estimated Value Chart -->
            <div class="col-md-6">
                <div class="shadow-sm card">
                    <x-card-header>
                        Estimated Total Value (US$) by Variety
                    </x-card-header>
                    <div class="card-body">
                        <div id="value-chart" x-show="hasValueData===true" style="height: 350px;"></div>
                        <x-no-data x-show="hasValueData===false" />
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <x-card-header>
                        Monthly Volume vs Value
                    </x-card-header>
                    <div class="card-body">
                        <div id="monthly-chart" x-show="hasMonthlyData===true" style="height: 400px;"></div>
                        <x-no-data x-show="hasMonthlyData===false" />
                    </div>
                </div>
            </div>
        </div>


        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <x-card-header>
                        Monthly Estimated Demand (Kg) by Variety
                    </x-card-header>
                    <div class="card-body">
                        <div id="stacked-demand-chart" x-show="hasStackedDemandData===true" style="height: 400px;">
                        </div>
                        <x-no-data x-show="hasStackedDemandData===false" />
                    </div>
                </div>
            </div>
        </div>



        <!-- Donut Chart: Value % by Country -->
        <div class="mt-4 row">
            <div class="col-md-6">
                <div class="shadow-sm card">
                    <x-card-header>
                        Value Contribution by Country (%)
                    </x-card-header>
                    <div class="card-body">
                        <div id="country-value-donut" x-show="hasCountryValueDonutData===true" style="height: 400px;">
                        </div>
                        <x-no-data x-show="hasCountryValueDonutData===false" />
                    </div>
                </div>
            </div>
            <!-- Grouped Bar Chart: Estimated Demand by Country & Variety -->

            <div class="col-6">
                <div class="shadow-sm card">
                    <x-card-header>
                        Estimated Demand (Kg) by Country & Variety
                    </x-card-header>
                    <div class="card-body">
                        <div id="country-variety-chart" x-show="hasCountryVarietyData===true" style="height: 400px;">
                        </div>
                        <x-no-data x-show="hasCountryVarietyData===false" />
                    </div>
                </div>
            </div>


        </div>
        <!-- Line Chart: Average Price per Kg Over Time -->
        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <x-card-header>
                        Average Price per Kg (MWK) Over Time
                    </x-card-header>
                    <div class="card-body">
                        <div id="price-trend-chart" x-show="hasPriceTrendData===true" style="height: 400px;"></div>
                        <x-no-data x-show="hasPriceTrendData===false" />
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>
@script
    <script>
        Alpine.data('dashboard2', () => ({
            data: $wire.entangle('data'),
            hasDemandData: true,
            demandChart: {
                instance: null,
                data: [],
            },
            demandChartCollection(data) {
                const safeParseFloat = val => isNaN(parseFloat(val)) ? 0 : parseFloat(val);

                const usdValues = data.map(item => safeParseFloat(item.usd_value));
                const volumesKg = data.map(item => safeParseFloat(item.volume_kg));
                const varieties = data.map((item, index) => item.variety_demanded ??
                    `Unknown ${index + 1}`);

                return {
                    volumesKg: volumesKg,
                    usdValues: usdValues,
                    varieties: varieties
                };
            },
            hasValueData: true,
            valueChart: {
                instance: null,
                data: [],
            },

            valuesChartCollection(data) {
                const safeParseFloat = val => isNaN(parseFloat(val)) ? 0 : parseFloat(val);

                const usdValues = data.map(item => safeParseFloat(item.usd_value));
                const volumesKg = data.map(item => safeParseFloat(item.volume_kg));
                const varieties = data.map((item, index) => item.variety_demanded ??
                    `Unknown ${index + 1}`);

                return {
                    volumesKg: volumesKg,
                    usdValues: usdValues,
                    varieties: varieties
                };
            },
            hasMonthlyData: true,
            monthlyChart: {
                instance: null,
                data: [],
            },
            monthlyChartCollection(data) {
                const safeParseFloat = val => isNaN(parseFloat(val)) ? 0 : parseFloat(val);

                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

                const usdValues = data.map(item => safeParseFloat(item.usd_value));
                const volumesKg = data.map(item => safeParseFloat(item.volume_kg));
                const months = data.map((item, index) => {
                    if (item.entry_date) {
                        const date = new Date(item.entry_date);
                        const month = monthNames[date.getMonth()];
                        const year = String(date.getFullYear()).slice(-2); // last two digits
                        return `${month}-${year}`;
                    } else {
                        return `Unknown ${index + 1}`;
                    }
                });

                return {
                    volumesKg: volumesKg,
                    usdValues: usdValues,
                    months: months
                };
            },
            hasStackedDemandData: true,
            stackedDemandChart: {
                instance: null,
                data: [],
            },

            stackedChartCollection(rawData) {
                // Step 1: Extract all unique varieties
                const uniqueVarieties = new Set();
                for (const entry of Object.values(rawData)) {
                    for (const key of Object.keys(entry)) {
                        uniqueVarieties.add(key.trim()); // normalize keys
                    }
                }

                // Step 2: Prepare empty arrays for each variety
                const seriesMap = {};
                for (const variety of uniqueVarieties) {
                    seriesMap[variety] = [];
                }

                // Step 3: Sort dates and populate data
                const dates = Object.keys(rawData).sort();
                const formattedDates = dates.map(dateStr => {
                    const date = new Date(dateStr);
                    return date.toLocaleString('en-US', {
                        month: 'short',
                        year: '2-digit'
                    }); // e.g. "May-24"
                });

                for (const date of dates) {
                    const entry = rawData[date];
                    for (const variety of uniqueVarieties) {
                        const matchKey = Object.keys(entry).find(k => k.trim() === variety);
                        const value = matchKey ? entry[matchKey] : 0;
                        seriesMap[variety].push(value);
                    }
                }

                // Step 4: Convert seriesMap to ApexCharts format
                const series = Array.from(uniqueVarieties).map(variety => ({
                    name: variety,
                    data: seriesMap[variety]
                }));

                return {
                    dates: formattedDates,
                    series: series
                }
            },
            hasDistrictComboData: true,
            districtComboChart: {
                instance: null,
                data: [],

            },
            districtComboChartCollection(data) {
                const safeParseFloat = val => isNaN(parseFloat(val)) ? 0 : parseFloat(val);

                const usdValues = data.map(item => safeParseFloat(item.usd_value));
                const volumesKg = data.map(item => safeParseFloat(item.volume_kg));
                const districts = data.map((item, index) => item.final_market_district ??
                    `District ${index + 1}`);

                return {
                    volumesKg: volumesKg,
                    usdValues: usdValues,
                    districts: districts
                };
            },
            hasCountryValueDonutData: true,
            countryValueDonut: {
                instance: null,
                data: []
            },
            countryValueDonutCollection(data) {
                const safeParseFloat = val => isNaN(parseFloat(val)) ? 0 : parseFloat(val);

                const usdValues = data.map(item => safeParseFloat(item.share));

                const countries = data.map((item, index) => item.country ??
                    `Country ${index + 1}`);

                return {

                    usdValues: usdValues,
                    countries: countries
                };
            },
            hasCountryVarietyData: true,
            countryVarietyChart: {
                instance: null,
                data: []
            },

            countryVarietyChartCollection(rawData) {
                // Step 1: Get unique country names across all varieties
                const countrySet = new Set();
                for (const varietyData of Object.values(rawData)) {
                    for (const country of Object.keys(varietyData)) {
                        countrySet.add(country);
                    }
                }
                const countries = Array.from(countrySet); // x-axis categories

                // Step 2: Build series array dynamically
                const series = Object.entries(rawData).map(([varietyName, countryData]) => {
                    return {
                        name: varietyName.trim(), // Remove trailing spaces like in "Violet "
                        data: countries.map(country => countryData[country] || 0) // Fill missing with 0
                    };
                });

                return {
                    countries: countries,
                    series: series

                }

            },
            hasPriceTrendData: true,
            priceTrendChart: {
                instance: null,
                data: []
            },
            priceTrendChartCollection(data) {
                const safeParseFloat = val => isNaN(parseFloat(val)) ? 0 : parseFloat(val);

                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

                const avgPrice = data.map(item => safeParseFloat(item.avg_price_per_kg));
                const volumesKg = data.map(item => safeParseFloat(item.volume_kg));
                const months = data.map((item, index) => {
                    if (item.entry_date) {
                        const date = new Date(item.entry_date);
                        const month = monthNames[date.getMonth()];
                        const year = String(date.getFullYear()).slice(-2); // last two digits
                        return `${month}-${year}`;
                    } else {
                        return `Unknown ${index + 1}`;
                    }
                });

                return {
                    volumesKg: volumesKg,
                    avgPrice: avgPrice,
                    months: months
                };
            },

            reRenderCharts(data) {

                // Check if data exists and is an array
                if (!data || !Array.isArray(data)) {
                    console.warn("No valid data provided to reRenderCharts");
                    return;
                }


                data.forEach(item => {
                    try {
                        // Skip if item is invalid or missing name
                        if (!item || typeof item !== 'object' || !('name' in item)) {
                            console.warn("Invalid chart data item - missing name", item);
                            return;
                        }

                        // Skip if data is missing
                        if (!('data' in item)) {
                            console.warn(`Chart ${item.name || 'unnamed'} has no data property`);
                            return;
                        }

                        switch (item.name) {
                            case 'volumeVsValueByDistrict':
                                if (item.data) {
                                    const chartData = this.districtComboChartCollection(item.data);
                                    this.updateDistrictComboChart(chartData);
                                    this.hasDemandData = true;
                                }
                                break;
                            case 'demandByVariety':
                                if (item.data) {
                                    const chartData = this.demandChartCollection(item.data);
                                    this.updateDemandChart(chartData);
                                }
                                break;
                            case 'valueByVariety':
                                if (item.data) {
                                    const chartData = this.valuesChartCollection(item.data);
                                    this.updateValueChart(chartData);
                                }
                                break;
                            case 'monthlyVolumeVsValue':
                                if (item.data) {
                                    const chartData = this.monthlyChartCollection(item.data);
                                    this.updateMonthlyChart(chartData);
                                }
                                break;
                            case 'monthlyDemandByVariety':
                                if (item.data) {
                                    const chartData = this.stackedChartCollection(item.data);
                                    this.updateStackedDemandChart(chartData);
                                }
                                break;
                            case 'countryValueShare':
                                if (item.data) {
                                    const chartData = this.countryValueDonutCollection(item.data);
                                    this.updateCountryValueDonut(chartData);
                                }
                                break;
                            case 'demandByCountryAndVariety':
                                if (item.data) {
                                    const chartData = this.countryVarietyChartCollection(item.data);
                                    this.updateCountryVarietyChart(chartData);
                                }
                                break;
                            case 'priceTrendMWK':
                                if (item.data) {
                                    const chartData = this.priceTrendChartCollection(item.data);
                                    this.updatePriceTrendChart(chartData);
                                }
                                break;


                            default:
                                console.warn(`Unknown chart type: ${item.name}`);

                        }
                    } catch (error) {
                        console.error(`Error processing chart data:`, error);
                    }
                });



            },

            updateDemandChart(data) {

                this.demandChart.updateOptions([{
                    series: [{
                        name: 'Estimated Demand (Kg)',
                        data: data.volumesKg
                    }],
                    xaxis: {
                        categories: data.varieties
                    },
                }])
                this.demandChart.data = data;
            },
            updateValueChart(data) {
                this.valueChart.updateOptions([{
                    series: data.usdValues,
                    labels: data.varieties
                }])
            },
            updateMonthlyChart(data) {
                this.monthlyChart.updateOptions([{
                    series: [{
                            name: 'Volume',
                            type: 'column',
                            data: data.volumesKg
                        },
                        {
                            name: 'Value ($)',
                            type: 'line',
                            data: data.usdValues
                        }
                    ],
                    xaxis: {
                        categories: data.months,
                        title: {
                            text: 'Month'
                        }
                    },
                }])
            },
            updateStackedDemandChart(data) {

                this.stackedDemandChart.updateOptions([{
                    series: data.series,
                    xaxis: {
                        categories: data.dates,

                    },
                }])
            },
            updateDistrictComboChart(data) {


                this.districtComboChart.instance.updateOptions({
                    series: [{
                            name: 'Volume (Kg)',
                            type: 'column',
                            data: data.volumesKg
                        },
                        {
                            name: 'Value (US$)',
                            type: 'line',
                            data: data.usdValues
                        }
                    ],
                    xaxis: {
                        categories: data.districts
                    }
                });
                this.districtComboChart.data = data;
            },
            updateCountryValueDonut(data) {
                this.countryValueDonut.updateOptions([{
                    series: data.usdValues,
                    labels: data.countries
                }])
            },
            updateCountryVarietyChart(data) {
                this.countryVarietyChart.updateOptions([{
                    series: data.series,
                    xaxis: {
                        categories: data.countries,
                    }
                }])
            },
            updatePriceTrendChart(data) {
                this.priceTrendChart.updateSeries([{
                    series: [{

                        data: data.avgPrice
                    }],
                    xaxis: {
                        categories: data.months,
                    }
                }])
            },
            init() {
                this.initDemandChart();
                this.initValueChart();
                this.initMonthlyChart();
                this.initStackedDemandChart();
                this.initDistrictComboChart();
                this.initCountryValueDonut();
                this.initCountryVarietyChart();
                this.initPriceTrendChart();


            },

            initDemandChart() {
                const filtered = this.data.filter(item => item.name === 'demandByVariety');

                // Check if filtered data exists and has data
                if (!filtered.length || !filtered[0].data) {
                    // Optionally clear the chart area or show a message

                    this.hasDemandData = false;
                    return;
                }

                const arr = filtered[0].data;
                const data = this.demandChartCollection(arr);

                this.demandChart = new ApexCharts(document.querySelector("#demand-chart"), {
                    chart: {
                        type: 'bar',
                        height: 390
                    },
                    series: [{
                        name: 'Estimated Demand (Kg)',
                        data: data.volumesKg
                    }],
                    xaxis: {
                        categories: data.varieties
                    },
                    colors: SystemColors
                });

                this.demandChart.render();
            },

            initValueChart() {
                const filtered = this.data.filter(item => item.name === 'valueByVariety');

                // Check if data exists and has entries
                if (!filtered.length || !filtered[0].data) {
                    this.hasValueData = false;
                    return;
                }

                const arr = filtered[0].data;
                const data = this.demandChartCollection(arr); // still using the same collection

                this.valueChart = new ApexCharts(document.querySelector("#value-chart"), {
                    chart: {
                        type: 'donut',
                        height: 400
                    },
                    series: data.usdValues,
                    labels: data.varieties,
                    tooltip: {
                        y: {
                            formatter: val => "$" + val.toLocaleString()
                        }
                    },
                    colors: SystemColors
                });

                this.valueChart.render();
            },

            initMonthlyChart() {
                const filtered = this.data.filter(item => item.name === 'monthlyVolumeVsValue');

                // Check if data exists and is not empty
                if (!filtered.length || !filtered[0].data) {
                    this.hasMonthlyData = false;
                    return;
                }

                const arr = filtered[0].data;
                const data = this.monthlyChartCollection(arr); // use same collection

                this.monthlyChart = new ApexCharts(document.querySelector("#monthly-chart"), {
                    chart: {
                        height: 400,
                        type: 'line',
                        stacked: false,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: 'Volume',
                            type: 'column',
                            data: data.volumesKg
                        },
                        {
                            name: 'Value ($)',
                            type: 'line',
                            data: data.usdValues
                        }
                    ],
                    xaxis: {
                        categories: data.months,
                        title: {
                            text: 'Month'
                        }
                    },
                    yaxis: [{
                            title: {
                                text: 'Volume'
                            },
                            labels: {
                                formatter: val => val.toLocaleString()
                            }
                        },
                        {
                            opposite: true,
                            title: {
                                text: 'Value ($)'
                            },
                            labels: {
                                formatter: val => '$' + val.toLocaleString()
                            }
                        }
                    ],
                    colors: SystemColors,
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [1],
                        formatter: (val, opts) =>
                            opts.seriesIndex === 1 ?
                            '$' + Math.round(val).toLocaleString() : Math.round(val).toLocaleString()
                    },
                    tooltip: {
                        shared: true,
                        y: [{
                                formatter: val => val.toLocaleString()
                            },
                            {
                                formatter: val => '$' + val.toLocaleString()
                            }
                        ]
                    },
                    legend: {
                        position: 'top'
                    }
                });

                this.monthlyChart.render();
            },

            initStackedDemandChart() {
                const filtered = this.data.filter(item => item.name === 'monthlyDemandByVariety');

                // Check if the data is available and has content
                if (!filtered.length || !filtered[0].data) {
                    this.hasStackedDemandData = false;
                    return;
                }

                const arr = filtered[0].data;

                const data = this.stackedChartCollection(arr); // same collection

                this.stackedDemandChart = new ApexCharts(document.querySelector("#stacked-demand-chart"), {
                    chart: {
                        type: 'line',
                        height: 400,
                        toolbar: {
                            show: false
                        }
                    },
                    series: data.series,
                    xaxis: {
                        categories: data.dates,
                        title: {
                            text: 'Period'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Volume (kg)'
                        },
                        labels: {
                            formatter: val => val.toLocaleString()
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: val => val.toLocaleString() + ' Kg'
                        }
                    },
                    colors: SystemColors,
                    legend: {
                        position: 'bottom'
                    },
                    dataLabels: {
                        enabled: false
                    }
                });

                this.stackedDemandChart.render();
            },

            initDistrictComboChart() {
                const filtered = this.data.filter(item => item.name === 'volumeVsValueByDistrict');

                // Check if valid data exists
                if (!filtered.length || !filtered[0].data) {
                    this.hasDistrictComboData = false;
                    return;
                }

                const arr = filtered[0].data;
                const data = this.districtComboChartCollection(arr);

                // Store chart data for potential later use
                this.districtComboChart.data = data;

                this.districtComboChart.instance = new ApexCharts(document.querySelector(
                    "#district-combo-chart"), {
                    chart: {
                        height: 500,
                        type: 'line',
                        toolbar: {
                            show: false
                        }
                    },
                    stroke: {
                        width: [0, 4]
                    },
                    series: [{
                            name: 'Volume (Kg)',
                            type: 'column',
                            data: data.volumesKg
                        },
                        {
                            name: 'Value (US$)',
                            type: 'line',
                            data: data.usdValues
                        }
                    ],
                    xaxis: {
                        categories: data.districts,
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '12px'
                            }
                        },
                        title: {
                            text: 'District'
                        }
                    },
                    yaxis: [{
                            title: {
                                text: 'Volume (Kg)'
                            },
                            labels: {
                                formatter: val => val.toLocaleString()
                            }
                        },
                        {
                            opposite: true,
                            title: {
                                text: 'Value (US$)'
                            },
                            labels: {
                                formatter: val => '$' + val.toLocaleString()
                            }
                        }
                    ],
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    colors: SystemColors,
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        position: 'top'
                    }
                });

                this.districtComboChart.instance.render();
            },

            initCountryValueDonut() {
                const filtered = this.data.filter(item => item.name === 'countryValueShare');

                // Check if data exists and is not empty
                if (!filtered.length || !filtered[0].data) {
                    this.hasCountryValueDonutData = false;
                    return;
                }

                const arr = filtered[0].data;
                const data = this.countryValueDonutCollection(arr);

                this.countryValueDonut = new ApexCharts(document.querySelector("#country-value-donut"), {
                    chart: {
                        type: 'donut',
                        height: 400
                    },
                    series: data.usdValues,
                    labels: data.countries,
                    colors: SystemColors,
                    dataLabels: {
                        formatter: val => val.toFixed(1) + '%'
                    },
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        y: {
                            formatter: val => val.toFixed(1) + '%'
                        }
                    }
                });

                this.countryValueDonut.render();
            },

            initCountryVarietyChart() {
                const filtered = this.data.filter(item => item.name === 'demandByCountryAndVariety');

                // Check if data exists and is not empty
                if (!filtered.length || !filtered[0].data) {
                    this.hasCountryVarietyData = false;
                    return;
                }

                const arr = filtered[0].data;
                const data = this.countryVarietyChartCollection(arr);

                this.countryVarietyChart = new ApexCharts(document.querySelector("#country-variety-chart"), {
                    chart: {
                        type: 'bar',
                        height: 400,
                        stacked: false,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%'
                        }
                    },
                    series: data.series,
                    xaxis: {
                        categories: data.countries,
                        title: {
                            text: 'Country'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Estimated Demand (Kg)'
                        },
                        labels: {
                            formatter: val => val.toLocaleString()
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: val => val.toLocaleString() + ' Kg'
                        }
                    },
                    legend: {
                        position: 'bottom'
                    },
                    colors: SystemColors,
                    dataLabels: {
                        enabled: false
                    }
                });

                this.countryVarietyChart.render();
            },

            initPriceTrendChart() {
                const filtered = this.data.filter(item => item.name === 'priceTrendMWK');

                // Check for valid, non-empty data
                if (!filtered.length || !filtered[0].data) {
                    this.hasPriceTrendData = false;
                    return;
                }

                const arr = filtered[0].data;
                const data = this.priceTrendChartCollection(arr);

                this.priceTrendChart = new ApexCharts(document.querySelector("#price-trend-chart"), {
                    chart: {
                        type: 'area',
                        height: 400,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Average Price (MWK/kg)',
                        data: data.avgPrice
                    }],
                    xaxis: {
                        categories: data.months,
                        title: {
                            text: 'Period'
                        },
                        labels: {
                            style: {
                                fontSize: '13px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'MWK per Kg'
                        },
                        labels: {
                            formatter: val => 'MWK ' + val.toFixed(2)
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: val => 'MWK ' + val.toFixed(2)
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: SystemColors,
                    markers: {
                        size: 5
                    },
                    legend: {
                        position: 'top'
                    }
                });

                this.priceTrendChart.render();
            }

        }));
    </script>
@endscript
