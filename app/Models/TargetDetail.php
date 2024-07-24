<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetDetail extends Model
{
    use HasFactory;
    protected $table = 'target_details';
    public function indicator_target()
    {
        return $this->belongsTo(IndicatorTarget::class, 'indicator_target_id');
    }
}
