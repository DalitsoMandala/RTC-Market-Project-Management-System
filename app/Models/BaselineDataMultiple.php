<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaselineDataMultiple extends Model
{
    use HasFactory;

    protected $table = 'baseline_data_multiple';
    protected $guarded = ['id'];
    public function baseline()
    {
        return $this->belongsTo(Baseline::class, 'baseline_data_id');
    }
}