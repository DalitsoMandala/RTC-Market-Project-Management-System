<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedReportData extends Model
{
  use HasFactory;

    protected $fillable = [
        'name',
        'value'
    ];

    /**
     * Get the systemReport that owns the SystemReportData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function systemReport()
    {
        return $this->belongsTo(AggregatedReport::class, 'aggregated_report_id');
    }
}