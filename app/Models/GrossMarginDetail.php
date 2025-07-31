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

    public function items()
    {
        return $this->hasMany(GrossMarginData::class,'gross_margin_detail_id');
    }
}
