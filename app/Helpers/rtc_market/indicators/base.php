<?php
namespace App\Helpers\rtc_market\indicators;

use App\Models\Indicator;
use App\Traits\FilterableQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class base
{
    use FilterableQuery;
    protected $financial_year;
    protected $reporting_period;
    protected $organisation_id;
    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year   = $financial_year;
        $this->organisation_id  = $organisation_id;
        $this->enterprise       = $enterprise;
    }
    public static function pullTotals($number): Collection
    {
        $indicator = Indicator::where('indicator_no', $number)->first();

        if (! $indicator) {
            return collect();
        }

        return $indicator->disaggregations
            ->pluck('name')
            ->mapWithKeys(fn($name) => [$name => 0]);
    }

    public function getTotalReport(Builder $query, Collection $data): Collection
    {
        $enterprise = $this->enterprise ?? null;

        // Use chunkById for better performance and memory safety on large datasets
        $query->chunkById(1000, function ($models) use ($data, $enterprise) {
            $models->each(function ($model) use ($data, $enterprise) {
                $json = json_decode($model->data, true);

                if (! is_array($json)) {
                    return;
                }

                foreach ($data as $key => $currentTotal) {
                    if ($this->shouldIncludeKey($key, $enterprise) && isset($json[$key])) {
                        $data->put($key, $currentTotal + $json[$key]);
                    }
                }
            });
        });

        return $data;
    }

    protected function shouldIncludeKey(string $key, ?string $enterprise): bool
    {
        if (! $enterprise) {
            return true;
        }

        $isEnterpriseKey = str_contains($key, 'Cassava') ||
        str_contains($key, 'Potato') ||
        str_contains($key, 'Sweet potato');

        if (! $isEnterpriseKey) {
            return true;
        }

        return str_contains($key, $enterprise);
    }

    protected function baseIndicator($number): ?Indicator
    {
        return Indicator::where('indicator_no', $number)->first();
    }

    protected function getIndicatorDisaggregations($number): Collection
    {
        $indicator = $this->baseIndicator($number);

        if (! $indicator) {
            return collect();
        }

        return $indicator->disaggregations->pluck('name')->mapWithKeys(fn($name) => [$name => 0]);
    }
}
