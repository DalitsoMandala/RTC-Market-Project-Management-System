<div>


    <button type="button" name="" id=""
        class="btn btn-soft-primary waves-effect waves-light my-2 @if ($this->exporting && !$this->exportFinished) pe-none opacity-50 @endif"
        wire:click='$dispatch("export-{{$this->namedExport}}")'>
        <i class="fas fa-file-excel"></i> Export



</div>

@if ($this->exporting && !$this->exportFinished)

    <div class="d-inline" wire:poll.5s="exportProgress">Exporting...please wait.</div>

    <div class="progress my-2">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;"
            aria-valuenow="{{$this->progress}}" aria-valuemin="0" aria-valuemax="100">

        </div>
    </div>

@endif

@if ($this->exportFinished && $this->exportFailed === false)
    <x-excelalert wire:click="downloadExport" />
@endif
@if ($this->exportFinished && $this->exportFailed)
    <x-excelalert-error />
@endif