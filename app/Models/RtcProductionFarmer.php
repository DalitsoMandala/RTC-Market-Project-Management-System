<?php

namespace App\Models;

use App\Livewire\tables\RtcMarket\RpmFarmerCultivation;
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

    public function members()
    {

        return $this->hasMany(RpmFarmerMember::class, 'rpmf_id');
    }

    public function employees()
    {
        return $this->hasMany(RpmFarmerEmployees::class, 'rpmf_id');
    }

    public function cultivatedArea()
    {

        return $this->hasMany(RpmFarmerCultivation::class, 'rpmf_id');
    }

    public function basicSeed()
    {

        return $this->hasMany(RpmFarmerBasicSeed::class, 'rpmf_id');
    }

    public function certifiedSeed()
    {

        return $this->hasMany(RpmFarmerCertifiedSeed::class, 'rpmf_id');
    }

    public function marketSegment()
    {
        return $this->hasMany(RpmFarmerMarketSegment::class, 'rpmf_id');
    }
}
