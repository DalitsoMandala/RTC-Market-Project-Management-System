<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedTarget extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function final_target()
    {
        return $this->belongsTo(IndicatorTarget::class, 'indicator_target_id');
    }

    /**
     * Get the user that owns the AssignedTarget
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

}
