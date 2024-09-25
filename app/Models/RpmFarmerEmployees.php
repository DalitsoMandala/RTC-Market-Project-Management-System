<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmFarmerEmployees extends Model
{
    use HasFactory;
    protected $table = 'rpmf_number_of_employees';
    protected $guarded = ['id'];

    public function farmers()
    {
        return $this->belongsTo(RtcProductionFarmer::class, 'rpmf_id');
    }
}
