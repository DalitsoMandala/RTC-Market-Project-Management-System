<div class="row align-items-center">
    <div class=" col-12 col-sm-6">

        <h3 class="card-title fs-4">{{ $this->nameOfTable ?? '' }}</h3>
        <p class="text-muted">{{ $this->descriptionOfTable ?? '' }}</p>
    </div>
    <div class="col-12 col-sm-6">
        <div class=" d-flex justify-content-end align-items-center">

            <button type="button" wire:loading.attr='disabled' wire:click='startExport()'
                @if ($this->exporting && !$this->exportFinished) disabled @endif
                class="btn btn-warning btn-label waves-effect waves-light"><i
                    class="align-middle bx bx-export label-icon fs-16 me-2"></i>
                @if ($this->exporting && !$this->exportFinished)
                    <span wire:poll.5s="exportProgress">Exporting...please wait!</span>
                @else
                    Export
                @endif
            </button>
        </div>





        @if ($this->exportFinished && $this->exportFailed)
            <div class="my-1">
                <x-excelalert-error />
            </div>
        @endif


    </div>
</div>
