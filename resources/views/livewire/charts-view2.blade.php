<div>

    <div class="my-4">

        <div class="my-2 row align-items-center ">
            <div class="col">
                <h2 class="h2">Marketing Overview</h2>
                <p class="text-muted">Summary of Demand, Value, and Distribution Trends</p>
            </div>
            <div class="col">
                <div class="d-flex justify-content-end">
                    <a href="marketing/manage-data" class="btn btn-warning"> View Details <i
                            class="bx bx-arrow-to-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Volume vs Value by District (Combo Chart) -->
        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="text-dark fw-bold card-header">
                        Estimated Volume (Kg) vs Value (US$) by District
                    </div>
                    <div class="card-body" x-data="districtComboChart">
                        <div id="district-combo-chart" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">

            <!-- Estimated Demand Chart -->
            <div class="col-md-6">
                <div class="shadow-sm card">
                    <div class="text-dark fw-bold card-header ">
                        Estimated Demand (Kg) by Variety
                    </div>
                    <div class="card-body" x-data="demandChart">
                        <div id="demand-chart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <!-- Estimated Value Chart -->
            <div class="col-md-6">
                <div class="shadow-sm card">
                    <div class="text-dark fw-bold card-header ">
                        Estimated Total Value (US$) by Variety
                    </div>
                    <div class="card-body" x-data="valueChart">
                        <div id="value-chart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="text-dark fw-bold card-header ">
                        Monthly Volume vs Value (May 2024 – Jan 2025)
                    </div>
                    <div class="card-body" x-data="monthlyChart">
                        <div id="monthly-chart" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="card-header text-dark fw-bold">
                        Monthly Estimated Demand (Kg) by Variety – Stacked View
                    </div>
                    <div class="card-body" x-data="stackedDemandChart">
                        <div id="stacked-demand-chart" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Donut Chart: Value % by Country -->
        <div class="mt-4 row">
            <div class="col-md-6">
                <div class="shadow-sm card">
                    <div class="text-dark fw-bold card-header">
                        Value Contribution by Country (%)
                    </div>
                    <div class="card-body" x-data="countryValueDonut">
                        <div id="country-value-donut" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
            <!-- Grouped Bar Chart: Estimated Demand by Country & Variety -->

            <div class="col-6">
                <div class="shadow-sm card">
                    <div class="text-dark fw-bold card-header">
                        Estimated Demand (Kg) by Country & Variety
                    </div>
                    <div class="card-body" x-data="countryVarietyChart">
                        <div id="country-variety-chart" style="height: 400px;"></div>
                    </div>
                </div>
            </div>


        </div>
        <!-- Line Chart: Average Price per Kg Over Time -->
        <div class="mt-4 row">
            <div class="col-12">
                <div class="shadow-sm card">
                    <div class="text-dark fw-bold card-header">
                        Average Price per Kg (MWK) Over Time
                    </div>
                    <div class="card-body" x-data="priceTrendChart">
                        <div id="price-trend-chart" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>
@script
    <script>
        Alpine.data('demandChart', () => ({
            init() {
                const options = {
                    chart: {
                        type: 'bar',
                        height: 390
                    },
                    series: [{
                        name: 'Estimated Demand (Kg)',
                        data: [10094350, 6484978, 1612100, 59060]
                    }],
                    xaxis: {
                        categories: ['Rosita', 'Violet', 'Chuma', 'White']
                    },
                    colors: SystemColors,
                };

                new ApexCharts(document.querySelector("#demand-chart"), options).render();
            }
        }));

        Alpine.data('valueChart', () => ({
            init() {
                const options = {
                    chart: {
                        type: 'donut',
                        height: 400
                    },
                    series: [5610650, 3414124, 1093275, 29632],
                    labels: ['Rosita', 'Violet', 'Chuma', 'White'],
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "$" + val.toLocaleString();
                            }
                        }
                    },
                    colors: SystemColors
                };

                new ApexCharts(document.querySelector("#value-chart"), options).render();
            }
        }));

        Alpine.data('monthlyChart', () => ({
            init() {
                const options = {
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
                            data: [2491530, 2775300, 4347006, 1197008, 2783991, 3278833, 1094940,
                                281880
                            ]
                        },
                        {
                            name: 'Value ($)',
                            type: 'line',
                            data: [1257308.395, 1537115.363, 2361909.88, 792190.0914, 1756997.744,
                                1651326.585, 546902.3415, 243930.3255
                            ]
                        }
                    ],
                    xaxis: {
                        categories: ['May-24', 'Jun-24', 'Jul-24', 'Aug-24', 'Sept-24', 'Oct-24', 'Nov-24',
                            'Jan-25'
                        ],
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
                        formatter: (val, opts) => {
                            return opts.seriesIndex === 1 ? '$' + Math.round(val).toLocaleString() :
                                Math.round(val).toLocaleString();
                        }
                    },
                    tooltip: {
                        shared: true,
                        y: [{
                            formatter: val => val.toLocaleString()
                        }, {
                            formatter: val => '$' + val.toLocaleString()
                        }]
                    },
                    legend: {
                        position: 'top'
                    }
                };

                new ApexCharts(this.$el.querySelector("#monthly-chart"), options).render();
            }
        }));

        Alpine.data('stackedDemandChart', () => ({
            init() {
                const options = {
                    chart: {
                        type: 'bar',
                        height: 400,
                        stacked: true,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: 'Chuma',
                            data: [290920, 58240, 309820, 203280, 336840, 305620, 107380, 0]
                        },
                        {
                            name: 'Rosita',
                            data: [1247260, 1392685, 2615740, 828410, 1427400, 1755975, 643160, 183720]
                        },
                        {
                            name: 'Violet',
                            data: [935710, 1324255, 1380146, 165318, 1019751, 1217238, 344400, 98160]
                        },
                        {
                            name: 'White',
                            data: [17640, 120, 41300, 0, 0, 0, 0, 0]
                        }
                    ],
                    xaxis: {
                        categories: ['May-24', 'Jun-24', 'Jul-24', 'Aug-24', 'Sept-24', 'Oct-24', 'Nov-24',
                            'Jan-25'
                        ],
                        title: {
                            text: 'Month'
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
                    colors: SystemColors,
                    legend: {
                        position: 'bottom'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false
                        }
                    }
                };

                new ApexCharts(this.$el.querySelector("#stacked-demand-chart"), options).render();
            }
        }));

        Alpine.data('districtComboChart', () => ({
            init() {
                const options = {
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
                            data: [
                                5629520, 4889036, 2713840, 1758060, 1477700, 793860, 565560,
                                298240, 58172, 22800, 12840, 6240, 6120, 5840, 4020, 3360, 2880,
                                2400
                            ]
                        },
                        {
                            name: 'Value (US$)',
                            type: 'line',
                            data: [
                                3707254.14, 2675796.745, 1300010.28, 882592.8041, 699474.586,
                                398693.3181, 235105.0828, 161195.8881, 31805.16848, 18961.73615,
                                11815.53398, 5318.103941, 5139.920046, 4363.221017, 2124.500286,
                                2958.309537, 3015.41976, 2055.968018
                            ]
                        }
                    ],
                    labels: [
                        'Other (Int.)', 'Lilongwe', 'Blantyre', 'Other (local)', 'Zomba',
                        'Mangochi', 'Ntcheu', 'Mzimba', 'Dowa', 'Mulanje', 'Balaka',
                        'Nkhatabay', 'Thyolo', 'Kasungu', 'Nkhotakota', 'Machinga',
                        'Mchinji', 'Mwanza'
                    ],
                    xaxis: {
                        categories: [
                            'Other (Int.)', 'Lilongwe', 'Blantyre', 'Other (local)', 'Zomba',
                            'Mangochi', 'Ntcheu', 'Mzimba', 'Dowa', 'Mulanje', 'Balaka',
                            'Nkhatabay', 'Thyolo', 'Kasungu', 'Nkhotakota', 'Machinga',
                            'Mchinji', 'Mwanza'
                        ],
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
                };

                new ApexCharts(this.$el.querySelector("#district-combo-chart"), options).render();
            }
        }));
        Alpine.data('countryValueDonut', () => ({
            init() {
                const options = {
                    chart: {
                        type: 'donut',
                        height: 400
                    },
                    series: [63, 16, 21],
                    labels: ['Malawi', 'Mozambique', 'Zambia'],
                    colors: SystemColors,
                    dataLabels: {
                        formatter: (val) => val.toFixed(1) + '%'
                    },
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        y: {
                            formatter: (val) => val.toFixed(1) + '%'
                        }
                    }
                };

                new ApexCharts(this.$el.querySelector("#country-value-donut"), options).render();
            }
        }));

        Alpine.data('countryVarietyChart', () => ({
            init() {
                const options = {
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
                            columnWidth: '55%',
                        }
                    },
                    series: [{
                            name: 'Chuma',
                            data: [6092410, 481880, 1130220]
                        },
                        {
                            name: 'Rosita',
                            data: [6469498, 2055520, 1946420]
                        },
                        {
                            name: 'Violet',
                            data: [59060, 5520, 9960]
                        },
                        {
                            name: 'White',
                            data: [0, 0, 0] // Malawi has 59,060, others have none, adjust as needed
                        }
                    ],
                    xaxis: {
                        categories: ['Malawi', 'Mozambique', 'Zambia'],
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
                        position: 'top'
                    },
                    colors: SystemColors,
                    dataLabels: {
                        enabled: false
                    }
                };

                new ApexCharts(this.$el.querySelector("#country-variety-chart"), options).render();
            }
        }));

        Alpine.data('priceTrendChart', () => ({
            init() {
                const options = {
                    chart: {
                        type: 'line',
                        height: 400,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Average Price (MWK/kg)',
                        data: [13.39, 21.58, 25.15, 47.70, 43.81, 34.97, 21.49, 127.91]
                    }],
                    xaxis: {
                        categories: ['May-24', 'Jun-24', 'Jul-24', 'Aug-24', 'Sept-24', 'Oct-24', 'Nov-24',
                            'Jan-25'
                        ],
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
                        enabled: true,
                        formatter: val => 'MWK ' + val.toFixed(2)
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
                };

                new ApexCharts(this.$el.querySelector("#price-trend-chart"), options).render();
            }
        }));
    </script>
@endscript
