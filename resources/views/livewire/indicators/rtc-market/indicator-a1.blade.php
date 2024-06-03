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
