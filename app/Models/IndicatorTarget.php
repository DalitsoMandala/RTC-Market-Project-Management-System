<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorTarget extends Model
{
    use HasFactory;

    public function assignedTargets()
    {
        return $this->hasMany(AssignedTarget::class, 'indicator_target_id');
    }

    public function details()
    {
        return $this->hasMany(TargetDetail::class, 'indicator_target_id');
    }


}
