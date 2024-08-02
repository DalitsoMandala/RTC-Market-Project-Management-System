<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorClass extends Model
{
    use HasFactory;


    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

}
