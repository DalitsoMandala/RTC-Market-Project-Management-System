<div>

    <x-upload-form-component :pageTitle="'Upload Production Marketing Log Data'" :formName="$form_name" :targetSet="$targetSet" :openSubmission="$openSubmission" :importing="$importing"
        :importingFinished="$importingFinished" :progress="$progress" :targetIds="$targetIds" :selectedMonth="$selectedMonth" :selectedFinancialYear="$selectedFinancialYear" :currentRoute="url()->current()">

        <form wire:submit='submitUpload'>
            <div x-data>
                <button class="btn btn-warning" type="button" @click="$wire.downloadTemplate()"
                    @if ($importing && !$importingFinished) disabled @endif wire:loading.attr='disabled'>
                    <!-- Border spinner -->
                    <div class="mx-2 text-white opacity-30 spinner-border" style="width: 1rem; height: 1rem;" wire:loading
                        wire:target='downloadTemplate' role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    Download template <i class="bx bx-download"></i>
                </button>
                <hr>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 @if ($importing) pe-none opacity-25 @endif">

                    {{-- File upload --}}
                    <x-filepond-single wire:model="upload" instantUpload="true" />

                    @error('upload')
                        <div class="d-flex justify-content-center">
                            <x-error class="text-center">
                                {{ $message }}
                            </x-error>
                        </div>
                    @enderror


                    {{-- Description --}}
                    <div class="mb-3" wire:loading.class="opacity-25 pe-none">
                        <label for="description" class="form-label">
                            Description

                            @hasanyrole('external|staff|enumerator')
                                (optional)
                            @endhasanyrole
                        </label>

                        <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" name="description"
                            id="description" rows="3"></textarea>

                        @error('description')
                            <x-error>
                                {{ $message }}
                            </x-error>
                        @enderror
                    </div>


                    {{-- Submit --}}
                    <div class="mt-5 d-flex justify-content-center" x-data="{
                        disableButton: false,
                        openSubmission: $wire.entangle('openSubmission')
                    }">
                        <button type="submit" class="px-5 btn btn-warning" wire:loading.attr="disabled"
                            wire:target="submitUpload,downloadTemplate" @uploading-files.window="disableButton = true"
                            @finished-uploading.window="disableButton = false"
                            :disabled="disableButton || openSubmission === false">

                            <div class="mx-2 opacity-30 spinner-border text-light" style="width: 1rem; height: 1rem;"
                                wire:loading wire:target="submitUpload,downloadTemplate" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>

                            Submit data
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </x-upload-form-component>
