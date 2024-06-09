<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtcProductionFarmer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->convertAttributesToUpper();
    //     });

    //     static::updating(function ($model) {
    //         $model->convertAttributesToUpper();
    //     });
    // }

    // Method to convert attributes to uppercase
    protected function convertAttributesToUpper()
    {
        foreach ($this->attributes as $key => $value) {
            if (is_string($value)) {
                $this->attributes[$key] = strtoupper($value);
            }
        }
    }
}