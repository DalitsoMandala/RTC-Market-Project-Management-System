<div>

    @section('title')
        Submit Market Data
    @endsection
    <div class="container-fluid">
  @php

        $routePrefix = Route::current()->getPrefix();

    @endphp
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"> Submit Market Data</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active"> Submit Market Data</li>

                       <li class="breadcrumb-item "> <a href="{{ $routePrefix }}/marketing/manage-data"> View Data</a> </li>

                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <x-alerts />

             
                <div class="card ">

                    <div class="card-body">
                        <div class="">
                            <h5> Instructions</h5>
                            <p class="alert bg-secondary-subtle text-uppercase">Download the template & upload your
                                data.</p>

                            @if ($importing && !$importingFinished)
                                <div class="alert alert-warning d-flex justify-content-between"
                                    wire:poll.5000ms='checkProgress()'>Importing your file
                                    Please wait....

                                    <div class="d-flex align-content-center">
                                        <span class="text-warning fw-bold me-2"> {{ $progress }}%</span>
                                        <div class="spinner-border text-warning spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>

                                <div x-data class="my-2 progress progress-sm">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                        role="progressbar" style="width: {{ $progress . '%' }}" aria-valuenow="25"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            @endif


                            <form wire:submit='submitUpload'>
                                <div x-data>
                                    <button class="btn btn-soft-warning" type="button"
                                        @click="$wire.downloadTemplate()"
                                        @if ($importing && !$importingFinished) disabled @endif wire:loading.attr='disabled'>
                                        <!-- Border spinner -->
                                        <div class="mx-2 opacity-30 spinner-border text-secondary"
                                            style="width: 1rem; height: 1rem;" wire:loading
                                            wire:target='downloadTemplate' role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        Download template <i class="bx bx-download"></i>
                                    </button>
                                    <hr>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-12 @if ($importing) pe-none opacity-25 @endif ">

                                        <x-filepond-single instantUpload="true" wire:model='upload' />


                                        @error('upload')
                                            <div class="d-flex justify-content-center">
                                                <x-error class="text-center">{{ $message }}</x-error>
                                            </div>
                                        @enderror

                                        <div class="mt-5 d-flex justify-content-center" x-data="{ disableButton: false, openSubmission: $wire.entangle('openSubmission') }">
                                            <button type="submit" @uploading-files.window="disableButton = true"
                                                @finished-uploading.window="disableButton = false"
                                                :disabled="disableButton === true || openSubmission === false"
                                                class="px-5 btn btn-warning">
                                                <!-- Border spinner -->
                                                <div class="mx-2 opacity-30 spinner-border text-light"
                                                    style="width: 1rem; height: 1rem;" wire:loading
                                                    wire:target='submitUpload' role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                Submit data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>


                    </div>
                </div>
            </div>
        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })
        $wire.on('hideModal', (e) => {
            const modals = document.querySelectorAll('.modal.show');

            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        })
        ">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




    </div>

</div>
