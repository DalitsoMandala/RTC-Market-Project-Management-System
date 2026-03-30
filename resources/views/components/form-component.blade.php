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


                    <div class="page-title-left col-12" wire:ignore>
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
                                <a href="/external/submission-period">Submission Periods</a>
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


                @if (!$bypassTargets)
                    @if (!$targetSet && isset($showTargetForm) && $showTargetForm)
                        <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                    @endif
                @endif

                @if (isset($openSubmission) && $openSubmission === false)
                    <div class="alert alert-danger" role="alert">
                        {{ $submissionClosedMessage ?? 'You can not submit a form right now because submissions are closed for the moment!' }}
                    </div>
                @endif

                <div
                    class="mb-1 row justify-content-center @if (isset($openSubmission) && $openSubmission === false) opacity-25 pe-none @endif">
                    <div class="col-md-8" x-data="typeof formDraft === 'function' ? formDraft() : {
                        showInfo: false,
                        isLoading: false,
                        clearDrafts: () => {}
                    }" @clear-drafts.window="clearDrafts()">
                        <div class="my-1" x-ref="draftAlert" x-show="showInfo">
                            <x-draft-notice />
                        </div>

                        <div x-data x-show="isLoading">
                            <x-spinner />
                        </div>

                        <form wire:submit.debounce.1000ms='save' id="mainForm" wire:loading.class="opacity-25 pe-none"
                            x-show="!isLoading" x-transition.duration.500ms>
                            <div class="card col-12 col-md-12">
                                <div class="card-body @if (auth()->user()->hasAnyRole('monitor')) pe-none opacity-50 @endif">
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
            Alpine.data('formDraft', (skipDraft = false) => ({
                form: {},
                showInfo: false,
                isLoading: false,
                userId: @json(auth()->user()->id),
                formName: @json($formName ?? 'default'),

                init() {
                    if (skipDraft) return;

                    // Check for existing draft on load
                    const saved = localStorage.getItem(this.getStorageKey());
                    if (saved) {
                        this.form = JSON.parse(saved);
                        this.showInfo = true;
                        // Optional: You could trigger a Livewire fill here
                    }
                },

                getStorageKey() {
                    return `formDraft_${this.formName}_${this.userId}`;
                },

                // Refactored: Cleaner logic for nested objects (e.g., items.0.name)
                extractNestedData(sourceData, prefix) {
                    const data = Object.keys(sourceData)
                        .filter(key => key.startsWith(`${prefix}.`))
                        .reduce((acc, key) => {
                            const [_, index, prop] = key.split('.');
                            if (!acc[index]) acc[index] = {};
                            acc[index][prop] = sourceData[key];
                            return acc;
                        }, []);

                    return {
                        count: data.filter(Boolean).length,
                        data: data.filter(Boolean)
                    };
                },

                saveDraft(event) {
                    if (skipDraft) return;

                    const input = event.target;
                    const modelKey = input.getAttribute('wire:model') || input.getAttribute('x-model');
                    if (!modelKey) return;

                    let value = input.type === 'checkbox' ? input.checked : input.value;

                    // Update local state and storage
                    this.form[modelKey] = value;
                    localStorage.setItem(this.getStorageKey(), JSON.stringify(this.form));
                },

                clearDrafts() {
                    localStorage.removeItem(this.getStorageKey());
                    this.showInfo = false;
                    this.form = {};
                    document.getElementById('mainForm')?.reset();
                },

                loadingIndicator() {
                    this.isLoading = true;
                    this.$wire.dispatch('update-form');
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 2000);
                }
            }))
        </script>
    @endscript
@else
    @script
        <script>
            // Minimal fallback so the UI doesn't break
            Alpine.data('formDraft', () => ({
                form: {},
                showInfo: false,
                isLoading: false,
                clearDrafts() {
                    document.getElementById('mainForm')?.reset();
                }
            }))
        </script>
    @endscript
@endif
