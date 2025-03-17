<div class="mx-2 d-flex justify-content-end">


    <button type="button" name="" id=""
        class="btn btn-soft-warning waves-effect waves-light my-2 @if ($this->exporting && !$this->exportFinished) disabled @endif"
        wire:click='$dispatch("export-{{ $this->namedExport }}")'>
        <i class="fas fa-file-excel"></i> Export



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
