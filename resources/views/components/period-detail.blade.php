<div>

    @php
        $period = null;
        $year = null;
        $months = \App\Models\ReportingPeriodMonth::find($attributes['period']);
        $projectYear = \App\Models\FinancialYear::find($attributes['year']);

        if ($months && $projectYear) {
            $period =
                $months->type != 'UNSPECIFIED'
                    ? "{$months->start_month} to {$months->end_month}"
                    : 'Unspecified Period';
            $year = 'Year ' . $projectYear->number;
        }

    @endphp
    <div class="my-2 card">
        <div class="card-body">


            <div class="row">
                <div class="col">
                    <div class="mb-3 ">
                        <label for="formId1">Submission Period</label>
                        <input type="text" class="form-control" readonly name="formId1" id="formId1"
                            value="{{ $period }}" />

                    </div>


                </div>
                <div class="col">
                    <div class="mb-3 ">
                        <label for="formId1">Project Year</label>
                        <input type="text" class="form-control" readonly name="formId1" id="formId1"
                            value="{{ $year }}" />

                    </div>


                </div>


            </div>
        </div>
    </div>
