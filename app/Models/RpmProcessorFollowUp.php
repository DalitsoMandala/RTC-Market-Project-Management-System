<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmProcessorFollowUp extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processors()
    {
        return $this->belongsTo(RtcProductionProcessor::class, 'rpm_processor_id');
    }
}
