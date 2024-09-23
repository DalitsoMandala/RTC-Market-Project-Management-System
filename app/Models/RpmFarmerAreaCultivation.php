<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerAreaCultivation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'rpmf_area_cultivation';
    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpmf_id');
    }

}
