<?php

namespace App\Models;

use App\Models\Indicator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicatorDisaggregation extends Model
{
    use HasFactory;
    protected $table = 'indicator_disaggregations';

    protected $guarded = ['id'];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }
}
