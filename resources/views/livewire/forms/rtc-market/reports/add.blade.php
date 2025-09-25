<div>

    @section('title')
        Add Aggregates (Report) Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
               

                    <div class="page-title-left col-12" wire:ignore>
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>

                            <li class="breadcrumb-item active">Submit Reports</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3" x-data="{
                        
                        }">
                            <label for="" class="form-label">Indicator for reports (select one) <sup
                                    class="text-danger">*</sup></label>
                            <select class="form-select" name="" id=""
                                wire:model.live.debounce.1000ms='selectedReportIndicator'>

                                @foreach ($reportIndicators as $indicators)
                                    <option selected value="{{ $indicators->id }}">
                                        ({{ $indicators->indicator_no }})
                                        {{ $indicators->indicator_name }}</option>
                                @endforeach
                            </select>
                            @error('selectedReportIndicator')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12">

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
                <div class="card @if ($openSubmission === false) opacity-25  pe-none @endif">
                    <div class="card-header">
                        <h4 class="card-title">Enter your data for : <span
                                class="text-warning">{{ $indicator->indicator_name }}</span> </h4>
                    </div>
                    <div class="card-body">
                        @php
                            $componentMap = [
                                'Percentage increase in value of formal RTC exports' => 'indicator-b2',
                                'Percentage of value ($) of formal RTC imports substituted through local production' =>
                                    'indicator-b3',
                                'Percentage increase in RTC investment' => 'indicator-b6',
                                'Percentage increase in adoption of new RTC technologies' => 'indicator-114',
                                'Percentage seed multipliers with formal registration' => 'indicator-223',
                                'Percentage business plans for the production of different classes of RTC seeds that are executed' =>
                                    'indicator-231',
                                'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' =>
                                    'indicator-325',
                            ];

                            $componentName = $componentMap[$indicator->indicator_name] ?? 'number-indicators';

                            $componentKey =
                                'component-' .
                                $array['indicator_id'] .
                                '-' .
                                $array['form_id'] .
                                '-' .
                                $array['submission_period_id'];
                        @endphp

                        @livewire(
                            "forms.rtc-market.reports.{$componentName}",
                            [
                                'form_id' => $array['form_id'],
                                'indicator_id' => $array['indicator_id'],
                                'financial_year_id' => $array['financial_year_id'],
                                'month_period_id' => $array['month_period_id'],
                                'submission_period_id' => $array['submission_period_id'],
                            ],
                            key($componentKey)
                        )
                    </div>

                </div>
            </div>


        </div>
    </div>

    <x-scroll-up />






</div>
