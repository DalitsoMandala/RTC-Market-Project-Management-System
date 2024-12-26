<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baseline extends Model
{
    use HasFactory;
    protected $table = "baseline_data";
    protected $guarded = ['id'];


    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function baselineMultiple()
    {
        return $this->hasMany(BaselineDataMultiple::class, 'baseline_id');
    }


}