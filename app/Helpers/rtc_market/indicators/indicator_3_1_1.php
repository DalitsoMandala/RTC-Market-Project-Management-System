<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Recruitment;
use Illuminate\Database\Eloquent\Builder;

class indicator_3_1_1 extends base
{

    public function builder(): Builder
    {
        return $this->applyFilters(
            Recruitment::query()->where('status', 'approved')
        );
    }

    public function getDisaggregations()
    {
        $stats = $this->builder()
            ->where('group', 'Producer organization (PO)')
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN enterprise = 'Potato' THEN 1 END) as potato,
                COUNT(CASE WHEN enterprise = 'Cassava' THEN 1 END) as cassava,
                COUNT(CASE WHEN enterprise = 'Sweet potato' THEN 1 END) as sweet_potato

            ")
            ->first();

        //We need to add market Segment

        $disaggregations = $this->getIndicatorDisaggregations('3.1.1');

        $disaggregations->put('Total', (int) ($stats->total ?? 0));
        $disaggregations->put('Potato', (int) ($stats->potato ?? 0));
        $disaggregations->put('Cassava', (int) ($stats->cassava ?? 0));
        $disaggregations->put('Sweet potato', (int) ($stats->sweet_potato ?? 0));

        return $disaggregations->toArray();
    }
}
