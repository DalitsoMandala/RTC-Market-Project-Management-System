<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerInterMarket extends Model
{
    use HasFactory;
    protected $table = "rpm_farmer_inter_markets";
    protected $guarded = ['id'];
    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpm_farmer_id');
    }
}
