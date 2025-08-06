<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMarginDetail extends Model
{
    use HasFactory;
       protected $guarded=['id'];

    public function grossMargin()
    {
        return $this->belongsTo(GrossMargin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

      protected static function booted()
    {
        static::creating(function ($model) {
            // Sequential numeric ID format
            $att = GrossMarginDetail::latest('id')->first();
            $number = $att ? $att->id + 1 : 1; // Increment based on the latest ID
            $model->gross_id = 'GRSS-' . str_pad($number, 5, '0', STR_PAD_LEFT); // Example: FARM-00001
        });
    }
    public function items()
    {
        return $this->hasMany(GrossMarginData::class,'gross_margin_detail_id');
    }
}
