<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div>

                    <h3 class="mb-5 text-center text-primary">RTC PRODUCTION AND MARKETING FORM (FARMERS)</h3>


                    @if ($openSubmission === false)
                        <div class="alert alert-warning" role="alert">
                            You can not submit a form right now
                            because submissions are closed for the moment!
                        </div>
                    @endif
                </div>

                <div class="my-2 border shadow-none card card-body">
                    <h5> Instructions</h5>
                    <p class="alert bg-info-subtle text-uppercase">Download the Rtc production Farmers template &
                        uploading your
                        data.</p>

                    <form wire:submit='submitUpload'>
                        <div x-data>
                            <a class="btn btn-soft-primary" href="#" data-toggle="modal" role="button"
                                @click="$wire.downloadTemplate()">
                                Download template <i class="bx bx-download"></i> </a>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col">
                                <x-alerts />.
                            </div>
                        </div>
                        <div id="table-form">
                            <div class="row">
                                <div class="col">

                                    @if ($importing && !$importingFinished)
                                        <div class="alert alert-warning" wire:poll.1500ms='checkErrors()'>Importing your
                                            file
                                            Please wait....</div>



                                        <div x-data="{
                                            progress: 0,
                                        
                                        
                                        }"
                                            @progress-update.window="progress = $event.detail.progress; ">

                                            <div x-show="progress > 0">
                                                <div class="d-flex justify-content-end">


                                                    <p class="fw-bolder text-primary"> <span
                                                            x-text="progress + '%'"></span></p>

                                                </div>

                                                <div x-data class="my-2 progress progress-sm">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                        role="progressbar" :style="{ width: progress + '%' }"
                                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">

                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                            <div class="row justify-content-center">

                                <div class="col-12 @if ($importing) pe-none opacity-25 @endif">
                                    <x-filepond-single instantUpload="true" wire:model='upload' />
                                    @error('upload')
                                        <div class="d-flex justify-content-center">
                                            <x-error class="text-center ">{{ $message }}</x-error>
                                        </div>
                                    @enderror
                                    <div class="mt-5 d-flex justify-content-center" x-data="{ disableButton: false, openSubmission: $wire.entangle('openSubmission') }">
                                        <button type="submit" @uploading-files.window="disableButton = true"
                                            @finished-uploading.window="disableButton = false"
                                            :disabled="disableButton === true || openSubmission === false"
                                            class="btn btn-primary ">
                                            Submit data
                                        </button>


                                    </div>


                                </div>
                            </div>

                        </div>
                    </form>

                    <small></small>
                </div>
            </div>
        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


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
