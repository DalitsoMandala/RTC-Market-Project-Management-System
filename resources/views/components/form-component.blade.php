<div>
    @section('title')
        {{ $title ?? 'Form' }}
    @endsection
    @php
        use Ramsey\Uuid\Uuid;
        use illuminate\Support\Facades\Route;
        $uuid = Uuid::uuid4()->toString();
        $currentUrl = url()->current();
        $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";
        $routePrefix = Route::current()->getPrefix();
        $formRoute = strtolower(str_replace(' ', '-', $formName));
    @endphp
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-block align-items-center justify-content-between">
                    <h4 class="mb-2">{{ $pageTitle ?? 'Add Data' }}</h4>

                    <div class="page-title-right" wire:ignore>
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Dashboard</a>
                            </li>
                            @role('admin')
                                <li class="breadcrumb-item">
                                    <a href="/admin/submission-period">Submission Periods</a>
                                </li>
                            @endrole
                            @role('manager')
                                <li class="breadcrumb-item">
                                    <a href="/cip/submission-period">Submission Periods</a>
                                </li>
                            @endrole

                            @role('external')
                                <li class="breadcrumb-item"></li>
                                <a href="/external/submission-periods">Submission Periods</a>
                                </li>
                            @endrole

                            <li class="breadcrumb-item active">Add Data</li>
                            <li class="breadcrumb-item">
                                <a href="{{ $replaceUrl }}">Upload Data</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ $routePrefix }}/forms/rtc-market/{{ $formRoute }}/view">
                                    {{ ucwords(strtolower($formName)) }} Data
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                @if (isset($formTitle))
                    <h3 class="my-5 text-center text-warning">{{ $formTitle }}</h3>
                @endif

                @if (isset($showAlpineAlerts) && $showAlpineAlerts)
                    <x-alpine-alerts />
                @else
                    <x-alerts />
                @endif



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
                                        <div class="mt-5 d-flex col-12 justify-content-center" x-data>
                                            <button class="mx-1 btn btn-secondary" type="reset" id="resetForm"
                                                @click="window.scrollTo({
                                                    top: 0,
                                                    behavior: 'smooth'
                                                })
                                                $wire.dispatch('show-alert',{
                                                    data : {
                                                        message : 'Form has been cleared.',
                                                        type : 'notice',
                                                    }
                                                })

                                                $dispatch('clear-drafts')
                                                ">Reset
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
                extractNestedData(sourceData, prefix) {
                    const indices = [];
                    const structuredData = [];

                    for (const key in sourceData) {
                        if (key.startsWith(`${prefix}.`)) {
                            const [_, index, property] = key.split('.');

                            // Track unique indices
                            if (!indices.includes(index)) {
                                indices.push(index);
                            }

                            // Build structured object
                            if (!structuredData[index]) {
                                structuredData[index] = {};
                            }
                            structuredData[index][property] = sourceData[key];
                        }
                    }

                    // Filter out empty slots (if any) and return
                    const filteredData = structuredData.filter(Boolean);
                    return {
                        count: indices.length,
                        data: filteredData,
                    };
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
                    document.getElementById('mainForm').reset();
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
