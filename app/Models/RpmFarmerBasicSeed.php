<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerBasicSeed extends Model
{
    use HasFactory;
    protected $guarded = [];


    protected $table = 'rpmf_basic_seed';

    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpmf_id');
    }
}
