<div>
    <h3 class="py-2">Targets</h3>
    <div class="row">
        @foreach ($data as $dt)
            <div class="col-3  ">
                <div class="border  shadow-none card @if ($dt->financial_year == $currentYear) border-success @endif ">
                    <div class=" card-header d-flex justify-content-between">
                        <h3 class="card-title" style="font-size: 14px">Year {{ $dt->financial_year }} </h3>
                        <p class="mb-0" style="font-size: 12px">
                            {{ \Carbon\Carbon::parse($dt->start_date)->format('d/F/Y') }} -
                            {{ \Carbon\Carbon::parse($dt->end_date)->format('d/F/Y') }}
                        </p>

                    </div>
                    <div class="card-body ">
                        <div x-data="{
                            target: @js($dt->target_value),
                            current: 0,
                        }" x-init="() => {
                            current = @js(collect($dt->data)->sum('current_value'));
                        }">

                            <p class="mb-1 text-uppercase fw-medium text-muted text-truncate text-center"> LOP</p>
                            <h4 class="mb-0 fs-22 fw-semibold ff-secondary text-center">
                                <span x-text="current+'/'+target"></span>
                            </h4>

                        </div>
                    </div>
                    <div class="card-footer">


                        <div class="py-2">

                            @foreach ($dt->data as $results)
                                <div class="mb-3 bars" x-data="{
                                    title: @js($results->organisation),
                                    current: @js($results->current_value),
                                    target: @js($results->target_value),
                                    calculatePercentage(value, total) {
                                        return Math.floor((value / total) * 100);
                                    }
                                }">
                                    <p class="mb-1"><span x-text="title"></span>
                                        (<span x-text="current+'/' + target"></span>)
                                        <span class="float-end"
                                            x-text="calculatePercentage(current, target) + '%'"></span>
                                    </p>
                                    <div class="mt-2 progress" style="height: 6px;">
                                        <div class="progress-bar progress-bar-animated progress-bar-striped bg-primary"
                                            role="progressbar"
                                            :class="{
                                                'bg-success': calculatePercentage(current, target) >= 50,
                                                'bg-warning': calculatePercentage(current, target) >= 25 &&
                                                    calculatePercentage(current, target) < 50,
                                                'bg-danger': calculatePercentage(current, target) >= 0 &&
                                                    calculatePercentage(current, target) < 25,
                                            }"
                                            :style="{
                                                'width': calculatePercentage(current, target) + '%'
                                            }"
                                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="75"></div>
                                    </div>

                                </div>
                            @endforeach


                            @if (count($dt->data) === 0)
                                <div class="alert alert-warning" role="alert">
                                    <strong>Not Assigned -</strong> Not available!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @endforeach

    </div>
    <hr>


</div>
