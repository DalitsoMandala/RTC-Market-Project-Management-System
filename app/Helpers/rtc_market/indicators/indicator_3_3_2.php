<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\AttendanceRegister;
use Illuminate\Database\Eloquent\Builder;

class indicator_3_3_2 extends base
{
    public function builder(): Builder
    {
        return $this->applyAttendanceFilters(
            AttendanceRegister::query()->where('status', 'approved')
        );
    }

    public function getDisaggregations(): array
    {
        $categories = ['Farmer', 'Processor', 'Partner', 'Staff', 'Aggregator', 'Transporter', 'Trader'];

        $stats = $this->builder()
            ->where('meetingCategory', 'Training')
            ->whereIn('category', $categories)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN rtcCrop_potato = 1 OR rtcCrop_potato = true THEN 1 ELSE 0 END) as potato,
                SUM(CASE WHEN rtcCrop_cassava = 1 OR rtcCrop_cassava = true THEN 1 ELSE 0 END) as cassava,
                SUM(CASE WHEN rtcCrop_sweet_potato = 1 OR rtcCrop_sweet_potato = true THEN 1 ELSE 0 END) as sweet_potato,
                SUM(CASE WHEN category = 'Farmer' THEN 1 ELSE 0 END) as farmers,
                SUM(CASE WHEN category = 'Processor' THEN 1 ELSE 0 END) as processors,
                SUM(CASE WHEN category = 'Partner' THEN 1 ELSE 0 END) as partners,
                SUM(CASE WHEN category = 'Staff' THEN 1 ELSE 0 END) as staff,
                SUM(CASE WHEN category = 'Aggregator' THEN 1 ELSE 0 END) as aggregators,
                SUM(CASE WHEN category = 'Transporter' THEN 1 ELSE 0 END) as transporters,
                SUM(CASE WHEN category = 'Trader' THEN 1 ELSE 0 END) as traders
            ")
            ->first();

        $disaggregations = $this->getIndicatorDisaggregations('3.3.2');

        // Total unique attendees matching filtered categories
        $disaggregations->put('Total', (int) ($stats->total ?? 0));

        // Category breakdown (Mutually exclusive - sums to Total)
        $disaggregations->put('Farmers', (int) ($stats->farmers ?? 0));
        $disaggregations->put('Processors', (int) ($stats->processors ?? 0));
        $disaggregations->put('Partners', (int) ($stats->partners ?? 0));
        $disaggregations->put('Staff', (int) ($stats->staff ?? 0));
        $disaggregations->put('Aggregators', (int) ($stats->aggregators ?? 0));
        $disaggregations->put('Transporters', (int) ($stats->transporters ?? 0));
        $disaggregations->put('Traders', (int) ($stats->traders ?? 0));

        // Crop breakdown (Multi-select / Overlapping counts)
        $disaggregations->put('Potato', (int) ($stats->potato ?? 0));
        $disaggregations->put('Cassava', (int) ($stats->cassava ?? 0));
        $disaggregations->put('Sweet potato', (int) ($stats->sweet_potato ?? 0));

        return $disaggregations->toArray();
    }
}
