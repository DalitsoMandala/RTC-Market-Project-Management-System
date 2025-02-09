<div x-data="{ show: false }">



    <div class="mx-2 d-flex justify-content-end">


        <button :disabled="show" type="button" name="" id=""
            class="btn btn-soft-warning  my-2 me-1  @if ($this->exporting && !$this->exportFinished) disabled @endif"
            wire:click='$dispatch("export-{{ $this->namedExport }}")'>
            <i class="fas fa-file-excel"></i> Export





            <button type="button" name="" id=""
                class="mx-2 my-2 btn btn-soft-warning waves-effect waves-light @if ($this->exporting && !$this->exportFinished) disabled @endif "
                @click="show = !show;">
                <i class="bx bx-import"></i> Import Report
            </button>







    </div>


    <div class="row" x-show="show">
        <livewire:imports.import-data />

    </div>


    @if ($this->exporting && !$this->exportFinished)
        <span class="mx-2 text-center d-block " wire:poll.5s="exportProgress">Exporting...please wait.</span>

        <div class="mx-2 my-2 progress progress-sm">
            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: 100%;" aria-valuenow="{{ $this->progress }}" aria-valuemin="0" aria-valuemax="100">

            </div>
        </div>
    @endif

    <div class="mx-2">
        @if ($this->exportFinished && $this->exportFailed === false)
            <x-excelalert wire:click="downloadExport" />
        @endif
        @if ($this->exportFinished && $this->exportFailed)
            <x-excelalert-error />
        @endif

    </div>

</div>
