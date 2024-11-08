<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageIncreaseDetails extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'percentage_increase_details';

    public function percentageIncrease()
    {
        return $this->belongsTo(PercentageIncreaseIndicator::class, 'percentage_id');
    }
}
