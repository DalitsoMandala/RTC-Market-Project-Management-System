<div>

    <div wire:loading.class='opacity-50 pe-none'>
        <button type="button" class="btn btn-warning  my-2" wire:click='$dispatch("export")'>
            <i class="fas fa-file-excel"></i> Export
        </button>

        <span wire:loading>Loading please wait...</span>
    </div>


</div>