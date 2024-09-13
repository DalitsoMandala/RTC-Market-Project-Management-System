<?php

namespace App\Models;

use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationHrc extends Model
{
    use HasFactory;

    protected $table = 'hrc_location';
    protected $guarded = ['id'];

    /**
     * Get the farmer that owns the LocationHrc
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function household()
    {
        return $this->belongsTo(HouseholdRtcConsumption::class, 'hrc_id');
    }
}
