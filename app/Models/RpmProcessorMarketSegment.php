<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RpmProcessorMarketSegment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'rpmp_market_segment';
    public function farmers()
    {
        return $this->belongsTo(RtcProductionProcessor::class, 'rpmp_id');
    }
}
