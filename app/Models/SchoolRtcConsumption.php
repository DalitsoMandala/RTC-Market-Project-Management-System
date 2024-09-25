<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRtcConsumption extends Model
{
    use HasFactory;
    protected $table = "school_rtc_consumption";
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Sequential numeric ID format
            $latestFarmer = SchoolRtcConsumption::latest('id')->first();
            $number = $latestFarmer ? $latestFarmer->id + 1 : 1; // Increment based on the latest ID
            $model->sc_id = 'SC-' . str_pad($number, 5, '0', STR_PAD_LEFT); // Example: FARM-00001
        });
    }
}
