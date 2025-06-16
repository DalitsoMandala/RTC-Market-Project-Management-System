<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seedRegistrations()
    {
        return $this->hasMany(FarmerSeedRegistration::class, 'recruitment_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Sequential numeric ID format
            $latestFarmer = Recruitment::latest('id')->first();
            $number = $latestFarmer ? $latestFarmer->id + 1 : 1; // Increment based on the latest ID
            $model->rc_id = 'RC-' . str_pad($number, 5, '0', STR_PAD_LEFT); // Example: FARM-00001
        });
    }
}
