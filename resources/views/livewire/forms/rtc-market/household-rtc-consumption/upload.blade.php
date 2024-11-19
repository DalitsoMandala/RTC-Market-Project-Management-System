<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Upload</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">


                <h3 class="mb-5 text-center text-warning">HOUSEHOLD RTC CONSUMPTION</h3>

                <x-alerts />






                @if (!$targetSet)
                    <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                @endif

                @if ($openSubmission === false)
                    <div class="alert alert-warning" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif

                <div class="my-2 border shadow-none card card-body @if ($openSubmission === false) opacity-25  pe-none @endif"
                    x-data="{
                        selectedFinancialYear: $wire.entangle('selectedFinancialYear'),
                        selectedMonth: $wire.entangle('selectedMonth'),
                        selectedIndicator: $wire.entangle('selectedIndicator'),
                    }">
                    <h5> Instructions</h5>
                    <p class="alert bg-secondary-subtle text-uppercase">Download the Household Rtc consumption template &
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
                                        wire:poll.5s='checkProgress()'>Importing your
                                        file
                                        Please wait....

                                        <div class=" d-flex align-content-center ">
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
                                <div class="mt-5 d-flex justify-content-center"
                                    x-data="{ disableButton: false, openSubmission: $wire.entangle('openSubmission') }">
                                    <button type="submit" @uploading-files.window="disableButton = true"
                                        @finished-uploading.window="disableButton = false"
                                        :disabled="disableButton === true || openSubmission === false"
                                        class="btn btn-warning px-5 ">
                                        Submit data
                                    </button>


                                </div>


                            </div>
                        </div>


                    </form>

                    <small></small>
                </div>
            </div>
        </div>







    </div>

</div>
@script
<script>
    $wire.on('complete-submission', () => {
        $wire.send();
    });
</script>
@endscript
