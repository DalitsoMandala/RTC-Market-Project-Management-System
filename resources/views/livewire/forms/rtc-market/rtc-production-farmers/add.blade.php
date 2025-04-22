<div>

    @section('title')
        Add Farmers Data
    @endsection
    <style>
        .sticky-side {
            position: sticky;
            top: 120px;

        }

        .nav-pills a:hover {
            background: #3980c0;
            color: white;
        }

        .iti__placeholder {
            color: red;
        }
    </style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Data</h4>

                    <div class="page-title-right" wire:ignore>
                        @php
                            use Ramsey\Uuid\Uuid;
                            $uuid = Uuid::uuid4()->toString();
                            $currentUrl = url()->current();
                            $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Add Data</li>

                            <li class="breadcrumb-item">
                                <a href="{{ $replaceUrl }}">Upload Data</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>


        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12 ">
                <h3 class="mb-5 text-center text-warning">RTC PRODUCTION AND MARKETING (FARMERS)</h3>



                <x-alerts />






                @if (!$targetSet)
                    <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                @endif

                @if ($openSubmission === false)
                    <div class="alert alert-danger" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif

                <div class="mb-1 row  @if ($openSubmission === false) opacity-25  pe-none @endif">




                    <div class="col-md-8" x-data="formDraft()" @clear-drafts.window='clearDrafts()'>
                        <div class="my-1" x-ref="draftAlert" x-show="showInfo">
                            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                                <strong>Draft in Progress</strong> - Your changes are saved temporarily until final
                                submission
                            </div>
                        </div>


                        <form wire:submit.debounce.1000ms='save' id="mainForm">
                            <div class="card col-12 col-md-12">
                                <div class="card-header fw-bold" id="section-0">FOLLOW UP SECTION</div>
                                <div class="card-body">
                                    <!-- Group Name -->
                                    <div class="mb-3">
                                        <label for="groupName" class="form-label">Group Name</label>
                                        <input type="text"
                                            class="form-control @error('location_data.group_name') is-invalid

                                        @enderror"
                                            id="groupName" wire:model='location_data.group_name'>
                                        @error('location_data.group_name')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>
                                    <div class="mb-3" x-data="{
                                        location_data: $wire.entangle('location_data'),
                                    }">
                                        <label for="" class="form-label ">ENTERPRISE</label>
                                        <div class="form-group">

                                            <select
                                                class="form-select @error('location_data.enterprise')
                                                is-invalid
                                            @enderror"
                                                x-model='location_data.enterprise'>
                                                <option value="">Select one</option>
                                                <option value="Cassava">Cassava</option>
                                                <option value="Potato">Potato</option>
                                                <option value="Sweet potato">Sweet potato</option>
                                            </select>
                                        </div>

                                        @error('location_data.enterprise')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="" class="form-label">DISTRICT</label>
                                        <select
                                            class="form-select @error('location_data.district')
                                            is-invalid
                                        @enderror"
                                            wire:model='location_data.district'>
                                            @include('layouts.district-options')
                                        </select>
                                        @error('location_data.district')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="" class="form-label">EPA</label>
                                        <x-text-input wire:model='location_data.epa' :class="$errors->has('location_data.epa') ? 'is-invalid' : ''" />
                                        @error('location_data.epa')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="" class="form-label">SECTION</label>
                                        <x-text-input wire:model='location_data.section' :class="$errors->has('location_data.section') ? 'is-invalid' : ''" />
                                        @error('location_data.section')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    @include('livewire.forms.rtc-market.rtc-production-farmers.first')

                                    @include('livewire.forms.rtc-market.rtc-production-farmers.repeats')

                                    <div class=" d-flex col-12 justify-content-center" x-data>
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
                                </div>
                            </div>

                        </form>


                    </div>

                    <div class="d-none d-md-block col-md-4 ">
                        <div class="card sticky-side">
                            <div class="card-body">
                                <nav class="nav nav-pills flex-column nav-fill ">
                                    <a class="nav-link disabled" aria-current="page" href="#section-0">Navigation
                                    </a>
                                    <a class="nav-link " aria-current="page" href="#section-0">FOLLOW UP SECTION</a>

                                    <a class="nav-link" href="#section-b" href="#">SECTION A: RTC
                                        PRODUCTION </a>

                                    <a class="nav-link" href="#section-c" href="#">SECTION B: RTC
                                        MARKETING</a>

                                    <a x-show="has_rtc_market_contract==1" x-data="{ has_rtc_market_contract: $wire.entangle('has_rtc_market_contract') }" class="nav-link"
                                        href="#section-f" href="#">CONTRACTUAL
                                        AGREEMENT</a>


                                    <a x-show="sells_to_domestic_markets == 1" x-data="{ sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets'), }" class="nav-link"
                                        href="#section-g" href="#">DOMESTIC
                                        MARKETS</a>
                                    <a x-show="sells_to_international_markets == 1" x-data="{
                                    
                                        sells_to_international_markets: $wire.entangle('sells_to_international_markets'),
                                    }"
                                        class="nav-link" href="#section-h" href="#">INTERNATIONAL
                                        MARKETS</a>
                                </nav>

                            </div>
                        </div>

                    </div>






                </div>

            </div>

        </div>



    </div>



</div>
@script
    <script>
        Alpine.data('formDraft', () => ({

            form: {},
            showInfo: false,
            userId: @json(auth()->user()->id),
            formName: @json($form_name),
            draftData: {},
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
                if (!modelKey) {
                    return;
                };

                // Get current value based on input type
                let value;
                if (input.type === 'checkbox') {
                    // Handle checkbox group
                    const checkboxes = document.querySelectorAll(
                        `[wire\\:model="${modelKey}"], [x-model="${modelKey}"]`);
                    const checkboxValues = [];

                    checkboxes.forEach((checkbox) => {
                        if (checkbox.checked) {
                            checkboxValues.push(checkbox.value); // Use 'on' if no value attribute
                        }
                    });

                    // If it's a single checkbox (not a group), just use boolean
                    value = checkboxes.length > 1 ? checkboxValues : input.checked;

                } else if (input.type === 'radio') {
                    value = input.checked ? input.value : undefined;
                } else {
                    value = input.value;
                }



                // Skip if radio button was unchecked
                if (value === undefined) {
                    return;
                };

                // Load existing draft first
                const currentDraft = JSON.parse(localStorage.getItem(this.draftName())) || {};

                // Update only the changed field
                currentDraft[modelKey] = value;

                // Save back to localStorage
                localStorage.setItem(this.draftName(), JSON.stringify(currentDraft));

                // Also update local form reference
                this.form = currentDraft;
            },

            clearDrafts() {
                localStorage.removeItem(this.draftName());


            },



            async init() {
                const draft = localStorage.getItem(this.draftName());
                const form = document.getElementById('mainForm');
                if (draft) {
                    this.showInfo = true;
                    let savedDraft = JSON.parse(draft);
                    this.draftData = savedDraft;

                    await this.$nextTick();
                    // First set all values
                    for (const key in savedDraft) {
                        let input = form.querySelector(
                            `[wire\\:model="${key}"], [x-model="${key}"]`);
                        if (input) {
                            // Wait for Alpine to be ready

                            if (input.type === 'checkbox') {
                                input.checked = savedDraft[key];
                            } else if (input.type === 'radio') {
                                input.checked = (input.value == savedDraft[key]);
                            } else {
                                input.value = savedDraft[key] || '';
                            }

                            // Force Alpine to recognize the change
                            input._x_model.set(savedDraft[key]);

                            // Trigger Livewire update if needed
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
            }

        }))
    </script>
@endscript
