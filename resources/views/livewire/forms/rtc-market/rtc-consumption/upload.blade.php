<x-upload-form-component :pageTitle="'Upload RTC Consumption Data'" :formName="$form_name" :targetSet="$targetSet" :openSubmission="$openSubmission" :importing="$importing"
    :importingFinished="$importingFinished" :progress="$progress" :targetIds="$targetIds" :selectedMonth="$selectedMonth" :selectedFinancialYear="$selectedFinancialYear" :currentRoute="url()->current()">



    <form wire:submit='submitUpload'>
        <div x-data>
            <button class="btn btn-soft-warning" type="button" @click="$wire.downloadTemplate()"
                @if ($importing && !$importingFinished) disabled @endif wire:loading.attr='disabled'>
                <!-- Border spinner -->
                <div class="mx-2 opacity-30 spinner-border text-secondary" style="width: 1rem; height: 1rem;" wire:loading
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
                        :disabled="disableButton === true || openSubmission === false" class="px-5 btn btn-warning">
                        <!-- Border spinner -->
                        <div class="mx-2 opacity-30 spinner-border text-light" style="width: 1rem; height: 1rem;"
                            wire:loading wire:target='submitUpload' role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        Submit data
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-upload-form-component>
