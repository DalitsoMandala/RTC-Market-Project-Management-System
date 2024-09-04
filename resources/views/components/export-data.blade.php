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
    <div class="alert alert-info" role="alert">
        Done. Download your file <a href="javascript: void(0);" class="alert-link" wire:click="downloadExport"> <span
                class="text-decoration-underline">Here</span></a>

    </div>
@endif
@if ($this->exportFinished && $this->exportFailed)
    <span class="text-danger">Failed to export! Something went wrong.</span>
@endif
