<div>
    @section('title')
        {{ $title ?? 'Form' }}
    @endsection

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ $pageTitle ?? 'Add Data' }}</h4>

                    <div class="page-title-right" wire:ignore>
                      
                        @isset($breadcrumbs)
                            <ol class="m-0 breadcrumb">
                                {{ $breadcrumbs }}
                            </ol>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                @if (isset($formTitle))
                    <h3 class="mb-5 text-center text-warning">{{ $formTitle }}</h3>
                @endif

                <x-alerts />

                @if (!$targetSet && isset($showTargetForm) && $showTargetForm)
                    <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                @endif

                @if (isset($openSubmission) && $openSubmission === false)
                    <div class="alert alert-danger" role="alert">
                        {{ $submissionClosedMessage ?? 'You can not submit a form right now because submissions are closed for the moment!' }}
                    </div>
                @endif

                <div
                    class="mb-1 row justify-content-center @if (isset($openSubmission) && $openSubmission === false) opacity-25 pe-none @endif">
                    <div class="col-md-8" x-data="formDraft()" @clear-drafts.window='clearDrafts()'>
                        <div class="my-1" x-ref="draftAlert" x-show="showInfo">
                            <x-draft-notice />
                        </div>

                        <div x-data x-show="isLoading">
                            <x-spinner />
                        </div>

                        <form wire:submit.debounce.1000ms='save' id="mainForm" wire:loading.class="opacity-25 pe-none"
                            x-show="!isLoading" x-transition.duration.500ms>
                            <div class="card col-12 col-md-12">
                                <div class="card-body">
                                    {{ $slot }}

                                    @if (!isset($hideSubmitButtons) || !$hideSubmitButtons)
                                        <div class="d-flex col-12 justify-content-center" x-data>
                                            <button class="mx-1 btn btn-secondary" type="reset"
                                                @click="clearDrafts(); window.scrollTo({
                                                    top: 0,
                                                    behavior: 'smooth'
                                                })">Reset
                                                Form</button>
                                            <button class="px-5 btn btn-warning"
                                                @click="window.scrollTo({
                                                top: 0,
                                                behavior: 'smooth'
                                            })"
                                                type="submit">Submit Data</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (!isset($skipDraftScript) || !$skipDraftScript)
    @script
        <script>
            Alpine.data('formDraft', () => ({
                form: {},
                showInfo: false,
                userId: @json(auth()->user()->id),
                formName: @json($formName ?? 'default'),
                draftData: {},
                isLoading: false,
                draftName: () => {
                    return 'formDraft' + this.formName + '-' + this.userId
                },

                saveDraft(event) {
                    const input = event.target;
                    const modelKey = input.getAttribute('wire:model') || input.getAttribute('x-model');
                    if (!modelKey) return;

                    let value;
                    if (input.type === 'checkbox') {
                        const checkboxes = document.querySelectorAll(
                            `[wire\\:model="${modelKey}"], [x-model="${modelKey}"]`);
                        const checkboxValues = [];
                        checkboxes.forEach((checkbox) => {
                            if (checkbox.checked) checkboxValues.push(checkbox.value);
                        });
                        value = checkboxes.length > 1 ? checkboxValues : input.checked;
                    } else if (input.type === 'radio') {
                        value = input.checked ? input.value : undefined;
                    } else {
                        value = input.value;
                    }

                    if (value === undefined) return;

                    const currentDraft = JSON.parse(localStorage.getItem(this.draftName())) || {};
                    currentDraft[modelKey] = value;
                    localStorage.setItem(this.draftName(), JSON.stringify(currentDraft));
                    this.form = currentDraft;
                },

                clearDrafts() {
                    localStorage.removeItem(this.draftName());
                    this.showInfo = false;
                    $dispatch('clear-error-bag')
                },

                loadingIndicator() {
                    $wire.$dispatch('update-form')
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 5000);
                },

                async init() {
                    this.isLoading = true;
                    const draft = localStorage.getItem(this.draftName());
                    const form = document.getElementById('mainForm');

                    if (draft) {
                        this.showInfo = true;
                        let savedDraft = JSON.parse(draft);
                        this.draftData = savedDraft;

                        await this.$nextTick();
                        for (const key in savedDraft) {
                            let input = form.querySelector(
                                `[wire\\:model="${key}"], [x-model="${key}"]`);
                            if (input) {
                                if (input.type === 'checkbox') {
                                    input.checked = savedDraft[key];
                                } else if (input.type === 'radio') {
                                    input.checked = (input.value == savedDraft[key]);
                                } else {
                                    input.value = savedDraft[key] || '';
                                }
                                input._x_model.set(savedDraft[key]);
                                if (input.getAttribute('wire:model')) {
                                    input.dispatchEvent(new Event('input', {
                                        bubbles: true
                                    }));
                                }
                            }
                        }
                    } else {
                        this.showInfo = false;
                        this.draftData = {};
                    }

                    form.addEventListener('input', (event) => this.saveDraft(event));
                    this.loadingIndicator()
                }
            }))
        </script>
    @endscript
@endif
