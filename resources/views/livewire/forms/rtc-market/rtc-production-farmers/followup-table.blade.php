<div class="table-responsive pb-5 col-md-12" style="margin: 10px 0 10px;" wire:ignore x-data="{ show: $wire.entangle('loadingData') }"
    :class="{ 'pe-none opacity-25': show === true }">
    <table class="table table-striped  nowrap align-middle w-100" id="rpmfollowup">
        <thead class="table-primary text-uppercase text-secondary" style="font-size: 12px">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Farmer ID</th>
                <th scope="col">Actor Name</th>

                <th scope="col">Enterprise</th>
                <th scope="col">District</th>
                <th scope="col">EPA</th>
                <th scope="col">Section</th>
                <th scope="col">Group Name</th>
                <th scope="col">Date of Follow-up</th>
                <th scope="col">Formatted Date of Follow-up</th>
                <th scope="col">Area Under Cultivation</th>
                <th scope="col">Area Under Cultivation Variety 1</th>
                <th scope="col">Area Under Cultivation Variety 2</th>
                <th scope="col">Area Under Cultivation Variety 3</th>
                <th scope="col">Area Under Cultivation Variety 4</th>
                <th scope="col">Area Under Cultivation Variety 5</th>

                <th scope="col">Plantlets Produced /Potato</th>
                <th scope="col">Plantlets Produced /Cassava</th>
                <th scope="col">Plantlets Produced /Sweet Potato</th>
                <th scope="col">Number of Screen House Vines Harvested</th>
                <th scope="col">Number of Screen House Mini Tubers Harvested</th>
                <th scope="col">Number of SAH Plants Produced</th>
                <th scope="col">Basic Seed Multiplication Total</th>
                <th scope="col">Basic Seed Multiplication Variety 1</th>
                <th scope="col">Basic Seed Multiplication Variety 2</th>
                <th scope="col">Basic Seed Multiplication Variety 3</th>
                <th scope="col">Basic Seed Multiplication Variety 4</th>
                <th scope="col">Basic Seed Multiplication Variety 5</th>
                <th scope="col">Basic Seed Multiplication Variety 6</th>
                <th scope="col">Basic Seed Multiplication Variety 7</th>
                <th scope="col">Certified Seed Multiplication Total</th>
                <th scope="col">Certified Seed Multiplication Variety 1</th>
                <th scope="col">Certified Seed Multiplication Variety 2</th>
                <th scope="col">Certified Seed Multiplication Variety 3</th>
                <th scope="col">Certified Seed Multiplication Variety 4</th>
                <th scope="col">Certified Seed Multiplication Variety 5</th>
                <th scope="col">Certified Seed Multiplication Variety 6</th>
                <th scope="col">Certified Seed Multiplication Variety 7</th>
                <th scope="col">Is Registered Seed Producer</th>
                <th scope="col">Seed Service Unit Registration Date</th>
                <th scope="col">Seed Service Unit Registration Number</th>

                <th scope="col">Uses Certified Seed</th>
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
            $('#rpmfollowup').DataTable()



            $wire.on('loaded-data-farmer', (e) => {
                populateFarmerTable(e.followUp);
            });

            function populateFarmerTable(data) {

                if ($.fn.DataTable.isDataTable('#rpmfollowup')) {
                    $('#rpmfollowup').DataTable().clear().destroy();
                }


                let tbody = $('#rpmfollowup tbody');
                tbody.empty(); // Clear any existing data
                let routePrefix = @js($prefix);
                data.forEach(function(row) {
                    let tr = $('<tr>');
                    tr.append($('<td>').text(row.id));
                    tr.append($('<td>').text(row.rpm_farmer_id));
                    tr.append($('<td>').text(row.actor_name));

                    tr.append($('<td>').text(row.enterprise));
                    tr.append($('<td>').text(row.district));
                    tr.append($('<td>').text(row.epa));
                    tr.append($('<td>').text(row.section));
                    tr.append($('<td>').text(row.group_name));
                    tr.append($('<td>').text(row.date_of_follow_up));
                    tr.append($('<td>').text(row.date_of_follow_up_formatted));
                    tr.append($('<td>').text(row.area_under_cultivation_total));
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
                    tr.append($('<td>').text(row.basic_seed_multiplication_total));
                    tr.append($('<td>').text(row.basic_seed_multiplication_variety_1));
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
                    tbody.append(tr);
                });

                let today = new Date();
                let dd = String(today.getDate()).padStart(2, '0');
                let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                let yyyy = today.getFullYear();
                today = mm + '_' + dd + '_' + yyyy;
                $('#rpmfollowup').DataTable({
                    // Your DataTable options here
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel" data-bs-toggle="tooltip" data-bs-placement="top" title="Export"></i>',
                        titleAttr: 'Excel',
                        title: 'Rtc Production and Marketing for farmers (Follow up) ' + today,
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
