<div>
    <div class="gap-1 d-grid" x-data="">


        <button type="button" class="btn btn-warning btn-sm"
            @click="$wire.dispatch('editData',{rowId : {{ $row->id }}})">
            EDIT
        </button>

        <button type="button" class="btn btn-warning btn-sm">
            ADD <i class="bx bx-plus"></i>
        </button>
        <button type="button" class="btn btn-warning btn-sm">
            UPLOAD <i class="bx bx-upload"></i>
        </button>
    </div>
</div>