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

                    <h3 class="mb-5 text-center text-primary">HOUSEHOLD CONSUMPTION FORM</h3>


                    @if ($openSubmission === false)
                        <div class="alert alert-warning" role="alert">
                            You can not submit a form right now
                            because submissions are closed for the moment!
                        </div>
                    @endif
                </div>

                <div class="my-2 border shadow-none card card-body">
                    <h5> Instructions</h5>
                    <p class="alert bg-info-subtle text-uppercase">Download the household
                        RTC
                        consumption template & uploading your
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

                                <x-alerts />
                            </div>
                        </div>
                        <div id="table-form">
                            <div class="row justify-content-center">

                                <div class="col-12 ">
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
                                            class="btn btn-primary">
                                            Submit data
                                        </button>


                                    </div>




                                </div>
                            </div>

                        </div>
                    </form>

                    <div>
                        @if ($importing && !$importFinished)
                            <div wire:poll.1000ms="updateImportProgress">Importing...please wait.</div>
                        @endif

                        @if ($importFinished)
                            Finished importing.
                        @endif
                    </div>


                </div>
            </div>
        </div>





    </div>

    @script
        <script>
            $wire.on('pollProgress', () => {
                setTimeout(function() {
                    $wire.checkProgress()
                }, 1000);
            });
        </script>
    @endscript
</div>
