<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerCertifiedSeed extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpmf_id');
    }
}

