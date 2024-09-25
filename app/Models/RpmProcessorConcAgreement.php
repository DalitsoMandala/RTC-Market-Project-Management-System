<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmProcessorConcAgreement extends Model
{
    use HasFactory;
    protected $guarded = ['id'];



    public function processors()
    {
        return $this->belongsTo(RtcProductionProcessor::class, 'rpm_processor_id');
    }
}
