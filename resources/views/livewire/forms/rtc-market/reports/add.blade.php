<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Data</h4>

                    <div class="page-title-right" wire:ignore>
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>

                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                @php

                @endphp


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
                            @if ($indicator->indicator_name == 'Percentage increase in value of formal RTC exports')
                                <livewire:forms.rtc-market.reports.indicator-b2 :form_id="$array['form_id']" :indicator_id="$array['indicator_id']"
                                    :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @elseif ($indicator->indicator_name == 'Percentage of value ($) of formal RTC imports substituted through local production')
                                <livewire:forms.rtc-market.reports.indicator-b3 :form_id="$array['form_id']" :indicator_id="$array['indicator_id']"
                                    :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @elseif ($indicator->indicator_name == 'Percentage increase in RTC investment')
                                <livewire:forms.rtc-market.reports.indicator-b6 :form_id="$array['form_id']" :indicator_id="$array['indicator_id']"
                                    :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @elseif ($indicator->indicator_name == 'Percentage increase in adoption of new RTC technologies')
                                <livewire:forms.rtc-market.reports.indicator-114 :form_id="$array['form_id']" :indicator_id="$array['indicator_id']"
                                    :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @elseif ($indicator->indicator_name == 'Percentage seed multipliers with formal registration')
                                <livewire:forms.rtc-market.reports.indicator-223 :form_id="$array['form_id']" :indicator_id="$array['indicator_id']"
                                    :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @elseif (
                                $indicator->indicator_name ==
                                    'Percentage business plans for the production of different classes of RTC seeds that are executed')
                                <livewire:forms.rtc-market.reports.indicator-231 :form_id="$array['form_id']" :indicator_id="$array['indicator_id']"
                                    :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @elseif (
                                $indicator->indicator_name ==
                                    'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)')
                                <livewire:forms.rtc-market.reports.indicator-325 :form_id="$array['form_id']" :indicator_id="$array['indicator_id']"
                                    :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @else
                                <livewire:forms.rtc-market.reports.number-indicators :form_id="$array['form_id']"
                                    :indicator_id="$array['indicator_id']" :financial_year_id="$array['financial_year_id']" :month_period_id="$array['month_period_id']" :submission_period_id="$array['submission_period_id']" />
                            @endif



                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <x-scroll-up />






</div>
