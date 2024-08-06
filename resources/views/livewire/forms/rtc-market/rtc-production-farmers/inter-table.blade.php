<div class="table-responsive pb-5 col-md-12" style="margin: 10px 0 10px;" wire:ignore x-data="{ show: $wire.entangle('loadingData') }"
    :class="{ 'pe-none opacity-25': show === true }">
    <table class="table table-striped  nowrap align-middle w-100" id="inter-market">
        <thead class="table-primary text-uppercase text-secondary" style="font-size: 12px">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">RPM Farmer ID</th>
                <th scope="col">Actor Name</th>
                <th scope="col">Date Recorded</th>
                <th scope="col">Crop Type</th>
                <th scope="col">Market Name</th>
                <th scope="col">Country</th>
                <th scope="col">Date of Maximum Sale</th>
                <th scope="col">Product Type</th>
                <th scope="col">Volume Sold Previous Period</th>
                <th scope="col">Financial Value of Sales</th>

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
            $('#inter-market').DataTable()



            $wire.on('loaded-data-farmer', (e) => {
                populateFarmerTable(e.inter);
            });

            function populateFarmerTable(data) {

                if ($.fn.DataTable.isDataTable('#inter-market')) {
                    $('#inter-market').DataTable().clear().destroy();
                }


                let tbody = $('#inter-market tbody');
                tbody.empty(); // Clear any existing data
                let routePrefix = @js($prefix);
                data.forEach(function(row) {
                    let tr = $('<tr>');
                    tr.append($('<td>').text(row.id));
                    tr.append($('<td>').text(row.rpm_farmer_id));
                    tr.append($('<td>').text(row.actor_name));
                    tr.append($('<td>').text(row.date_recorded_formatted));
                    tr.append($('<td>').text(row.crop_type));
                    tr.append($('<td>').text(row.market_name));
                    tr.append($('<td>').text(row.country));
                    tr.append($('<td>').text(row.date_of_maximum_sale_formatted));
                    tr.append($('<td>').text(row.product_type));
                    tr.append($('<td>').text(row.volume_sold_previous_period));
                    tr.append($('<td>').text(row.financial_value_of_sales));

                    tbody.append(tr);
                });

                let today = new Date();
                let dd = String(today.getDate()).padStart(2, '0');
                let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                let yyyy = today.getFullYear();
                today = mm + '_' + dd + '_' + yyyy;
                $('#inter-market').DataTable({
                    // Your DataTable options here
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel" data-bs-toggle="tooltip" data-bs-placement="top" title="Export"></i>',
                        titleAttr: 'Excel',
                        title: 'Rtc Production and Marketing for farmers (International markets) ' +
                            today,
                        className: 'bg-primary'
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
