<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtcProductionProcessor extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function followups()
    {
        return $this->hasMany(RpmProcessorFollowUp::class, 'rpm_processor_id');
    }

    public function doms()
    {
        return $this->hasMany(RpmProcessorDomMarket::class, 'rpm_processor_id');
    }

    public function intermarkets()
    {
        return $this->hasMany(RpmProcessorInterMarket::class, 'rpm_processor_id');
    }

    public function agreements()
    {
        return $this->hasMany(RpmProcessorConcAgreement::class, 'rpm_processor_id');
    }

    public function marketSegment()
    {
        return $this->hasMany(RpmProcessorMarketSegment::class, 'rpmp_id');
    }

    public function marketInformationSystems()
    {
        return $this->hasMany(RpmProcessorMarketInformationSystem::class, 'rpmp_id');
    }

    public function aggregationCenters()
    {
        return $this->hasMany(RpmProcessorAggregationCenter::class, 'rpmp_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Sequential numeric ID format
            $latestFarmer = RtcProductionProcessor::latest('id')->first();
            $number = $latestFarmer ? $latestFarmer->id + 1 : 1; // Increment based on the latest ID
            $model->pp_id = 'PP-' . str_pad($number, 5, '0', STR_PAD_LEFT); // Example: FARM-00001
        });
    }
}
