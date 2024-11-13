<div class="table-responsive pb-5 col-md-12" style="margin: 10px 0 10px;" wire:ignore x-data="{ show: $wire.entangle('loadingData') }"
    :class="{ 'pe-none opacity-25': show === true }">
    <table class="table table-striped  nowrap align-middle w-100" id="rpmf">
        <thead class="table-primary text-uppercase text-secondary" style="font-size: 12px">
            <tr>
                <th>Action</th>
                <th scope="col">Id</th>
                <th scope="col">Date of recruitment</th>
                <th scope="col">Name of actor</th>
                <th scope="col">Name of representative</th>
                <th scope="col">Phone number</th>
                <th scope="col">Type</th>
                <th scope="col">Approach</th>
                <th scope="col">Enterprise</th>
                <th scope="col">District</th>
                <th scope="col">EPA</th>
                <th scope="col">Section</th>
                <th scope="col">Sector</th>

                <th scope="col">Number of members total</th>
                <th scope="col">Number of members female 18-35</th>
                <th scope="col">Number of members male 18-35</th>
                <th scope="col">Number of members male 35+</th>
                <th scope="col">Number of members female 35+</th>
                <th scope="col">Group</th>
                <th scope="col">Establishment status</th>
                <th scope="col">Is registered</th>

                <th scope="col">Registration details /body</th>
                <th scope="col">Registration details /date</th>
                <th scope="col">Registration details /number</th>

                <th scope="col">Number of employees /Formal total</th>
                <th scope="col">Number of employees /Formal male 18-35</th>
                <th scope="col">Number of employees /Formal female 18-35</th>
                <th scope="col">Number of employees /Formal male 35+</th>
                <th scope="col">Number of employees /Formal female 35+</th>
                <th scope="col">Number of employees /Informal total</th>
                <th scope="col">Number of employees /Informal male 18-35</th>
                <th scope="col">Number of employees /Informal female 18-35</th>
                <th scope="col">Number of employees /Informal male 35+</th>
                <th scope="col">Number of employees /Informal female 35+</th>

                <th scope="col">Area under cultivation /variety 1</th>
                <th scope="col">Area under cultivation /variety 2</th>
                <th scope="col">Area under cultivation /variety 3</th>
                <th scope="col">Area under cultivation /variety 4</th>
                <th scope="col">Area under cultivation /variety 5</th>

                <th scope="col">Number of plantlets produced /Potato</th>
                <th scope="col">Number of plantlets produced /Cassava</th>
                <th scope="col">Number of plantlets produced /Sweet Potato</th>
                <th scope="col">Number of screen house vines harvested</th>
                <th scope="col">Number of screen house min tubers harvested</th>
                <th scope="col">Number of SAH plants produced</th>

                <th scope="col">Basic seed multiplication total</th>
                <th scope="col">Basic seed multiplication /variety 1</th>
                <th scope="col">Basic seed multiplication /variety 2</th>
                <th scope="col">Basic seed multiplication /variety 3</th>
                <th scope="col">Basic seed multiplication /variety 4</th>
                <th scope="col">Basic seed multiplication /variety 5</th>
                <th scope="col">Basic seed multiplication /variety 6</th>
                <th scope="col">Basic seed multiplication /variety 7</th>

                <th scope="col">Certified seed multiplication total</th>
                <th scope="col">Certified seed multiplication /variety 1</th>
                <th scope="col">Certified seed multiplication /variety 2</th>
                <th scope="col">Certified seed multiplication /variety 3</th>
                <th scope="col">Certified seed multiplication /variety 4</th>
                <th scope="col">Certified seed multiplication /variety 5</th>
                <th scope="col">Certified seed multiplication /variety 6</th>
                <th scope="col">Certified seed multiplication /variety 7</th>
                <th scope="col">Is registered seed producer</th>
                <th scope="col">Seed service unit registration details date</th>
                <th scope="col">Seed service unit registration details number</th>

                <th scope="col">Uses certified seed</th>
                <th scope="col">Market segment /Fresh</th>
                <th scope="col">Market segment /Processed</th>
                <th scope="col">Has RTC market contract</th>
                <th scope="col">Total vol production previous season</th>
                <th scope="col">Total production value previous season total</th>
                <th scope="col">Total production value previous season date</th>
                <th scope="col">Total vol irrigation production previous season</th>
                <th scope="col">Total irrigation production value previous season
                    total
                </th>
                <th scope="col">Total irrigation production value previous season
                    date
                </th>
                <th scope="col">Sells to domestic markets</th>
                <th scope="col">Sells to international markets</th>
                <th scope="col">Uses market information systems</th>
                <th scope="col">Market information systems</th>
                <th scope="col">Aggregation centers response</th>
                <th scope="col">Aggregation centers specify</th>
                <th scope="col">Aggregation center sales</th>
            </tr>
        </thead>
        <tbody style="font-size: 11px" class="animate__animated animate__fadeIn">

        </tbody>
    </table>


</div>
@php
    $prefix = Route::current()->getPrefix();
@endphp
@script
    <script>
        $(document).ready(function() {
            $('#rpmf').DataTable()



            $wire.on('loaded-data-farmer', (e) => {
                populateFarmerTable(e.data);
            });

            function populateFarmerTable(data) {

                if ($.fn.DataTable.isDataTable('#rpmf')) {
                    $('#rpmf').DataTable().clear().destroy();
                }


                let tbody = $('#rpmf tbody');
                tbody.empty(); // Clear any existing data
                let routePrefix = @js($prefix);
                data.forEach(function(row) {
                    let tr = $('<tr>');
                    tr.append($('<td>').html(
                        "<a class='btn btn-sm btn-warning' href='" + routePrefix +
                        "/forms/rtc-market/rtc-production-and-marketing-form-farmers/followup/" +
                        row.id + "'>Add follow up</a>"
                    ));
                    tr.append($('<td>').text(row.id));
                    tr.append($('<td>').text(row.date_of_recruitment));
                    tr.append($('<td>').text(row.name_of_actor));
                    tr.append($('<td>').text(row.name_of_representative));
                    tr.append($('<td>').text(row.phone_number));
                    tr.append($('<td>').text(row.type));
                    tr.append($('<td>').text(row.approach));
                    tr.append($('<td>').text(row.enterprise));
                    tr.append($('<td>').text(row.district));
                    tr.append($('<td>').text(row.epa));
                    tr.append($('<td>').text(row.section));
                    tr.append($('<td>').text(row.sector));

                    tr.append($('<td>').text(row.number_of_members_female_18_35 + row
                        .number_of_members_male_18_35 + row.number_of_members_male_35_plus + row
                        .number_of_members_female_35_plus));
                    tr.append($('<td>').text(row.number_of_members_female_18_35));
                    tr.append($('<td>').text(row.number_of_members_male_18_35));
                    tr.append($('<td>').text(row.number_of_members_male_35_plus));
                    tr.append($('<td>').text(row.number_of_members_female_35_plus));
                    tr.append($('<td>').text(row.group));
                    tr.append($('<td>').text(row.establishment_status));
                    tr.append($('<td>').text(row.is_registered));

                    tr.append($('<td>').text(row.registration_details_body));
                    tr.append($('<td>').text(row.registration_details_date));
                    tr.append($('<td>').text(row.registration_details_number));
                    tr.append($('<td>').text(row.formal_male_18_35 + row.formal_female_18_35 + row
                        .formal_male_35_plus + row.formal_female_35_plus));
                    tr.append($('<td>').text(row.formal_male_18_35));
                    tr.append($('<td>').text(row.formal_female_18_35));
                    tr.append($('<td>').text(row.formal_male_35_plus));
                    tr.append($('<td>').text(row.formal_female_35_plus));
                    tr.append($('<td>').text(row.informal_female_18_35 + row.informal_female_35_plus + row
                        .informal_male_18_35 + row.informal_male_35_plus));
                    tr.append($('<td>').text(row.informal_male_18_35));
                    tr.append($('<td>').text(row.informal_female_18_35));
                    tr.append($('<td>').text(row.informal_male_35_plus));
                    tr.append($('<td>').text(row.informal_female_35_plus));

                    tr.append($('<td>').text(row.area_under_cultivation_variety_1));
                    tr.append($('<td>').text(row.area_under_cultivation_variety_2));
                    tr.append($('<td>').text(row.area_under_cultivation_variety_3));
                    tr.append($('<td>').text(row.area_under_cultivation_variety_4));
                    tr.append($('<td>').text(row.area_under_cultivation_variety_5));

                    tr.append($('<td>').text(row.number_of_plantlets_produced_potato));
                    tr.append($('<td>').text(row.number_of_plantlets_produced_cassava));
                    tr.append($('<td>').text(row.number_of_plantlets_produced_sw_potato));
                    tr.append($('<td>').text(row.number_of_screen_house_vines_harvested));
                    tr.append($('<td>').text(row.number_of_screen_house_min_tubers_harvested));
                    tr.append($('<td>').text(row.number_of_sah_plants_produced));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_total));

                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_1)); // specify
                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_2));
                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_3));
                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_4));
                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_5));
                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_6));
                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_7));

                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_total));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_variety_1));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_variety_2));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_variety_3));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_variety_4));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_variety_5));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_variety_6));
                    tr.append($('<td>').text(row.area_under_certified_seed_multiplication_variety_7));
                    tr.append($('<td>').text(row.is_registered_seed_producer));
                    tr.append($('<td>').text(row.seed_service_unit_registration_details_date));
                    tr.append($('<td>').text(row.seed_service_unit_registration_details_number));

                    tr.append($('<td>').text(row.uses_certified_seed));
                    tr.append($('<td>').text(row.market_segment_fresh));
                    tr.append($('<td>').text(row.market_segment_processed));
                    tr.append($('<td>').text(row.has_rtc_market_contract));
                    tr.append($('<td>').text(row.total_vol_production_previous_season));
                    tr.append($('<td>').text(row.total_production_value_previous_season_total));
                    tr.append($('<td>').text(row.total_production_value_previous_season_date));
                    tr.append($('<td>').text(row.total_vol_irrigation_production_previous_season));
                    tr.append($('<td>').text(row.total_irrigation_production_value_previous_season_total));
                    tr.append($('<td>').text(row.total_irrigation_production_value_previous_season_date));
                    tr.append($('<td>').text(row.sells_to_domestic_markets));
                    tr.append($('<td>').text(row.sells_to_international_markets));
                    tr.append($('<td>').text(row.uses_market_information_systems));
                    tr.append($('<td>').text(row.market_information_systems));
                    tr.append($('<td>').text(row.aggregation_centers_response));
                    tr.append($('<td>').text(row.aggregation_centers_specify));
                    tr.append($('<td>').text(row.aggregation_center_sales));
                    tbody.append(tr);
                });

                let today = new Date();
                let dd = String(today.getDate()).padStart(2, '0');
                let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                let yyyy = today.getFullYear();
                today = mm + '_' + dd + '_' + yyyy;
                $('#rpmf').DataTable({
                    // Your DataTable options here
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel" data-bs-toggle="tooltip" data-bs-placement="top" title="Export"></i>',
                        titleAttr: 'Excel',
                        title: 'Rtc Production and Marketing for farmers ' + today,
                        className: 'bg-warning'
                    }],
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    responsive: true,

                });
            }
        });
    </script>
@endscript
