<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\SeedBeneficiary;
use Illuminate\Database\Eloquent\Builder;

class indicator_2_1_3 extends base
{
    public function builder(): Builder
    {
        return $this->applySeedFilters(
            SeedBeneficiary::query()->where('status', 'approved')
        );
    }

    public function getDisaggregations()
    {
        $stats = $this->builder()

            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN crop = 'Potato' THEN 1 END) as potato,
                COUNT(CASE WHEN crop = 'Cassava' THEN 1 END) as cassava,
                COUNT(CASE WHEN crop = 'Sweet potato' THEN 1 END) as sweet_potato
            ")
            ->first();

        $disaggregations = $this->getIndicatorDisaggregations('2.1.3');

        $disaggregations->put('Total', (int) ($stats->total ?? 0));
        $disaggregations->put('Potato', (int) ($stats->potato ?? 0));
        $disaggregations->put('Cassava', (int) ($stats->cassava ?? 0));
        $disaggregations->put('Sweet potato', (int) ($stats->sweet_potato ?? 0));

        return $disaggregations->toArray();
    }

}
