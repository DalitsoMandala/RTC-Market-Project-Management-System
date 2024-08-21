<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorTarget extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function assignedTargets()
    {
        return $this->hasMany(AssignedTarget::class, 'indicator_target_id');
    }

    public function details()
    {
        return $this->hasMany(TargetDetail::class, 'indicator_target_id');
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id', 'id');
    }


}
