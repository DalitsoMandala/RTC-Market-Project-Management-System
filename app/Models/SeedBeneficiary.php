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
}
