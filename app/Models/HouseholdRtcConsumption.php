<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdRtcConsumption extends Model
{
    use HasFactory;
    protected $table = 'household_rtc_consumption';
    protected $guarded = ['id'];

    public function location()
    {
        return $this->hasOne(LocationHrc::class, 'hrc_id');
    }

    public function mainFoods()
    {
        return $this->hasMany(MainFoodHrc::class, 'hrc_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Sequential numeric ID format
            $latestFarmer = HouseholdRtcConsumption::latest('id')->first();
            $number = $latestFarmer ? $latestFarmer->id + 1 : 1; // Increment based on the latest ID
            $model->hh_id = 'HH-' . str_pad($number, 5, '0', STR_PAD_LEFT); // Example: FARM-00001
        });
    }

}
