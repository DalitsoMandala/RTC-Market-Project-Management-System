<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdRtcConsumption extends Model
{
    use HasFactory;
    protected $table = 'household_rtc_consumption';
    protected $guarded = [];

    public function location()
    {
        return $this->belongsTo(HrcLocation::class, 'location_id');
    }

    public function mainFoods()
    {
        return $this->hasMany(HrcMainFood::class, 'hrc_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
