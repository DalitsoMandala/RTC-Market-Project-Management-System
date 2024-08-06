<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerDomMarket extends Model
{
    use HasFactory;
    protected $table = "rpm_farmer_dom_markets";
    protected $guarded = [];
    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpm_farmer_id');
    }
}