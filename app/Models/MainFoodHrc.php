<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainFoodHrc extends Model
{
    use HasFactory;
    protected $table = 'hrc_rtc_main_food';
    protected $guarded = ['id'];

    public function household()
    {
        return $this->belongsTo(HouseholdRtcConsumption::class, 'hrc_id');
    }
}
