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
}