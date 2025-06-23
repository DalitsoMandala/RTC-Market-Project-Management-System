<!-- Bootstrap 5 Alert with Download Button and Boxicons Icon -->
<div class="alert alert-success alert-border-left d-flex align-items-center" role="alert">
    <i class='bx bx-info-circle me-2'></i>
    <div class="flex-grow-1">
        Your file is ready for download! <a wire:loading.class='disabled' href="javascript: void(0);" {{ $attributes }}
            class="fw-bold ms-1 text-success">
            <u>Download here!</u></a>
    </div>

    <button type="button" class="btn-close ms-2 custom-tooltip" data-bs-dismiss="alert" aria-label="Close"
        title="Close"></button>
</div>
