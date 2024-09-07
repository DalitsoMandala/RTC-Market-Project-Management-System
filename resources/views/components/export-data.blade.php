<div>


    <button type="button" name="" id=""
        class="btn btn-soft-primary waves-effect waves-light my-2 @if ($this->exporting && !$this->exportFinished) pe-none opacity-50 @endif"
        wire:click='$dispatch("export")'>
        <i class="fas fa-file-excel"></i> Export



</div>

@if ($this->exporting && !$this->exportFinished)
    <div class="d-inline" wire:poll.5s="updateExportProgress">Exporting...please wait.</div>
@endif



@if ($this->exportFinished && $this->exportFailed === false)
    <x-excelalert wire:click="downloadExport" />
@endif
@if ($this->exportFinished && $this->exportFailed)
    <x-excelalert-error />
@endif
