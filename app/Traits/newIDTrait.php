<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait newIDTrait
{
    //



    public function validateNewIdForFarmers($prefix = "farmer_id_mapping1", $cacheKey, $row, $rowKey = "Farmer ID")
    {
        $mappedId = Cache::get($prefix . '_' . $cacheKey . '_' . $row[$rowKey]);
        if (!$mappedId) {
            Log::error("ID not found row: " . json_encode($row));
            return 0;
        } else {
            return $mappedId;
        }
    }

    public function validateNewIdForProcessors($prefix = "processor_id_mapping", $cacheKey, $row, $rowKey = "Processor ID")
    {
        $mappedId = Cache::get($prefix . '_' . $cacheKey . '_' . $row[$rowKey]);
        if (!$mappedId) {
            Log::error("ID not found row: " . json_encode($row));
            return 0;
        } else {
            return $mappedId;
        }
    }

    public function validateNewIdForRecruits($prefix, $cacheKey, $row, $rowKey)
    {
        $mappedId = Cache::get($prefix . '_' . $cacheKey . '_' . $row[$rowKey]);
        if (!$mappedId) {
            Log::error("ID not found row: " . json_encode($row));
            return 0;
        } else {
            return $mappedId;
        }
    }
}
