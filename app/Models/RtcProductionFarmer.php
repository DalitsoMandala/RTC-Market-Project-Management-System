<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtcProductionFarmer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = "rtc_production_farmers";
    public function followups()
    {
        return $this->hasMany(RpmFarmerFollowUp::class, 'rpm_farmer_id');
    }

    public function doms()
    {
        return $this->hasMany(RpmFarmerDomMarket::class, 'rpm_farmer_id');
    }

    public function intermarkets()
    {
        return $this->hasMany(RpmFarmerInterMarket::class, 'rpm_farmer_id');
    }

    public function agreements()
    {
        return $this->hasMany(RpmFarmerConcAgreement::class, 'rpm_farmer_id');
    }
    protected function convertAttributesToUpper()
    {
        foreach ($this->attributes as $key => $value) {
            if (is_string($value)) {
                $this->attributes[$key] = strtoupper($value);
            }
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
