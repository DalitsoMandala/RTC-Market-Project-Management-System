<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmProcessorAggregationCenter extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'rpmp_aggregation_centers';

    public function processors()
    {
        return $this->belongsTo(RtcProductionProcessor::class, 'rpmp_id');
    }
}
