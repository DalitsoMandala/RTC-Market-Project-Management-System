<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrcMainFood extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'hrc_main_food';

    public function hrc()
    {
        return $this->belongsTo(HouseholdRtcConsumption::class, 'hrc_id');
    }
}
