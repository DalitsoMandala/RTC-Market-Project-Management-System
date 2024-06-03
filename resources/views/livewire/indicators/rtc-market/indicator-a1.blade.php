<div>

    <div class="table-responsive" x-data="{
    
        downloadForm() {
            // $('#table').table2excel({ filename: 'excel_sheet-name.xls' });
            wb = XLSX.utils.table_to_book(document.getElementById('actor_by_crop'));
            XLSX.writeFile(wb, 'SheetJSTable.xlsx');
        }
    }">
        <a id="export_rtc_actor_by_crop" @click="downloadForm()" class="my-2 btn btn-primary download_table" href="#"
            role="button">Download
            this table <i class="bx bx-caret-down"></i></a>

        <table class="table text-uppercase table-bordered table-hover " id="actor_by_crop">
            <thead>
                <tr>
                    <th style="text-align: center" colspan="9">RTC Actor By Crop </th>
                </tr>
            </thead>

            <thead class="table-light">
                <tr>
                    <th scope="col"></th>
                    <th scope="col" colspan="2">Farmer</th>

                    <th scope="col" colspan="2">Processor</th>

                    <th scope="col" colspan="2">Trader</th>

                    <th scope="col" colspan="2">Grand Total</th>


                </tr>
            </thead>
            <thead class="table-light">
                <tr>
                    <th scope="col">CROP</th>
                    <th scope="col">VALUE</th>
                    <th scope="col">%</th>
                    <th scope="col">VALUE</th>
                    <th scope="col">%</th>
                    <th scope="col">VALUE</th>
                    <th scope="col">%</th>
                    <th scope="col" colspan="2">Total(Values)</th>


                </tr>
            </thead>
            <tbody>


                <tr class="">

                    <td scope="row">cassava</td>
                    <td>{{ $data['farmerCropCount']['cassava_count'] ?? 0 }}</td>
                    <td>{{ $data['farmerCropCountPercentage']['cassava_count'] ?? 0 }}</td>
                    <td>{{ $data['processorCropCount']['cassava_count'] ?? 0 }}</td>
                    <td>{{ $data['processorCropCountPercentage']['cassava_count'] ?? 0 }}</td>
                    <td>{{ $data['traderCropCount']['cassava_count'] ?? 0 }}</td>
                    <td>{{ $data['traderCropCountPercentage']['cassava_count'] ?? 0 }}</td>
                    <td>{{ $data['cassavaCount'] }}</td>
                    <td>
                    </td>
                </tr>
                <tr class="">
                    <td scope="row">potato</td>
                    <td>{{ $data['farmerCropCount']['potato_count'] ?? 0 }}</td>
                    <td>{{ $data['farmerCropCountPercentage']['potato_count'] ?? 0 }}</td>
                    <td>{{ $data['processorCropCount']['potato_count'] ?? 0 }}</td>
                    <td>{{ $data['processorCropCountPercentage']['potato_count'] ?? 0 }}</td>
                    <td>{{ $data['traderCropCount']['potato_count'] ?? 0 }}</td>
                    <td>{{ $data['traderCropCountPercentage']['potato_count'] ?? 0 }}</td>
                    <td>{{ $data['potatoCount'] }}</td>
                    <td>
                    </td>
                </tr>
                <tr class="">
                    <td scope="row">sweet potato</td>
                    <td>{{ $data['farmerCropCount']['sw_potato_count'] ?? 0 }}</td>
                    <td>{{ $data['farmerCropCountPercentage']['cassava_count'] ?? 0 }}</td>
                    <td>{{ $data['processorCropCount']['sw_potato_count'] ?? 0 }}</td>
                    <td>{{ $data['processorCropCountPercentage']['sw_potato_count'] ?? 0 }}</td>
                    <td>{{ $data['traderCropCount']['sw_potato_count'] ?? 0 }}</td>
                    <td>{{ $data['traderCropCountPercentage']['sw_potato_count'] ?? 0 }}</td>
                    <td>{{ $data['swPotatoCount'] }}</td>
                    <td>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <th>Grand Total</th>
                <th>{{ $data['farmerCropCountTotal'] }}</th>
                <th>{{ collect($data['farmerCropCountPercentage'])->sum() }}</th>
                <th>{{ $data['processorCropCountTotal'] }}</th>
                <th>{{ collect($data['processorCropCountPercentage'])->sum() }} </th>
                <th>{{ $data['traderCropCountTotal'] }}</th>
                <th>{{ collect($data['traderCropCountPercentage'])->sum() }} </th>
                <th>
                    {{ $data['cropCount'] }}
                </th>


            </tfoot>
        </table>


    </div>
    @assets
        <script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
    @endassets

    @script
        <script>
            document.getElementById("sheetjsexport").addEventListener('click', function() {
                /* Create worksheet from HTML DOM TABLE */

            });
        </script>
    @endscript
</div>
