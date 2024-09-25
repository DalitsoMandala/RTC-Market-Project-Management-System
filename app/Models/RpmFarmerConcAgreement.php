<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerConcAgreement extends Model
{
    use HasFactory;
    protected $table = "rpm_farmer_conc_agreements";
    protected $guarded = ['id'];
    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpm_farmer_id');
    }


}
