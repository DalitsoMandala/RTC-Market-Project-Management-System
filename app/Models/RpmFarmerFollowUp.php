<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerFollowUp extends Model
{
    use HasFactory;

    protected $table = "rpm_farmer_follow_ups";
    protected $guarded = [];

    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpm_farmer_id');
    }
}