<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseholdRtcConsumption extends Model
{
    use HasFactory;
    protected $table = 'household_rtc_consumption';
    protected $guarded = [];

    public function location()
    {
        return $this->belongsTo(HrcLocation::class, 'location_id');
    }

}
