<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LopTarget extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function Indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }
}
