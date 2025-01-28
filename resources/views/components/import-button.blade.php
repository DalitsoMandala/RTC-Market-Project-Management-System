<div x-data="{ show: false }">


    <div class="row">
        <div class="col d-flex justify-content-end">
            <button type="button" name="" id="" class="mx-2 btn btn-soft-warning waves-effect waves-light "
                @click="show = !show">
                <i class="bx bx-import"></i> Import Report
            </button>

        </div>



    </div>

    <div class="row" x-show="show">
        <livewire:imports.import-data>

    </div>



</div>
