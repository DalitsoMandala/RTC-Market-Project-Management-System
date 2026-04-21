<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeedBeneficiary extends Model
{
    use HasFactory;

    protected $guarded = [];
    /**
     * Get the user that owns the SeedBeneficiary
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function financial_year()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }
    public function period_month()
    {
        return $this->belongsTo(ReportingPeriodMonth::class, 'period_month_id');
    }

 public function organisation()
    {
        return $this->belongsTo(Organisation::class,'organisation_id');
    }
    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class,'financial_year_id');
    }
    public function periodMonth()
    {
        return $this->belongsTo(ReportingPeriodMonth::class,'period_month_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Sequential numeric ID format
            $latestFarmer = SeedBeneficiary::latest('id')->first();
            $number = $latestFarmer ? $latestFarmer->id + 1 : 1; // Increment based on the latest ID
            $model->sd_id = 'SD-' . str_pad($number, 5, '0', STR_PAD_LEFT); // Example: FARM-00001
        });
    }
}
