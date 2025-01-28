<div>
    <div class="col">
        <div class="mx-2 my-2 border shadow-none card border-light">
            <div class="card-body">
                <div class="my-2 border shadow-none card card-body " x-data="{
                    selectedFinancialYear: $wire.entangle('selectedFinancialYear'),
                    selectedMonth: $wire.entangle('selectedMonth'),
                    selectedIndicator: $wire.entangle('selectedIndicator'),
                }">
                    <h5> Instructions</h5>
                    <p class="alert bg-secondary-subtle text-uppercase">Download the Report template
                        &
                        upload your
                        data.</p>

                    <form wire:submit='submitUpload'>
                        <div x-data>
                            <a class="btn btn-soft-warning" href="#" data-toggle="modal" role="button"
                                @click="$wire.downloadTemplate()">
                                Download template <i class="bx bx-download"></i> </a>
                            <hr>
                        </div>

                        <div id="table-form">
                            <div class="row">
                                <div class="col">


                                </div>
                                @if ($importing && !$importingFinished)
                                    <div class="alert alert-warning d-flex justify-content-between"
                                        wire:poll.5000ms='checkProgress()'>Importing your
                                        file
                                        Please wait....

                                        <div class=" d-flex align-content-center">
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
                                        class="px-5 btn btn-warning ">
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
