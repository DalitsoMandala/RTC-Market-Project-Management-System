<!-- Modal Body-->
<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
    aria-hidden="true" wire:ignore.self data-bs-backdrop='static'>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>

        </div>
    </div>
</div>
