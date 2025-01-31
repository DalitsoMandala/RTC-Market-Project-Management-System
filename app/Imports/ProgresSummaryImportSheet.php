<?php

namespace App\Imports;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\SubmissionTarget;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class ProgresSummaryImportSheet implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $check = [];
        $multipleTargets = [];
        $collection->each(function ($row) use (&$multipleTargets) {
            $indicator = Indicator::with('disaggregations')->where('indicator_name', $row['Indicator'])->first();
            if ($indicator) {
                $disaggregations = $indicator->disaggregations;
            }
        });
    }
}
