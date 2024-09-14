<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerMarketInformationSystem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'rpmf_mis';
    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpmf_id');
    }
}
