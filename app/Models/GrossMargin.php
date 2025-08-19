<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMargin extends Model
{
    use HasFactory;
    public function items()
    {
        return $this->hasMany(GrossMarginItemValue::class, 'gross_margin_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function organisation()
    {
        return $this->belongsTo(Organisation::class,'organisation_id');
    }
}
