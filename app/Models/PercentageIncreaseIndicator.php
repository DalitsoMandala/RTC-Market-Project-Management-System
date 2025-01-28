<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageIncreaseIndicator extends Model
{
    use HasFactory;
    protected $table = "percentage_increase_indicators";
    protected $guarded = [];


    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }
}
