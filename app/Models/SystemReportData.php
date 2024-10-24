<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemReportData extends Model
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
        return $this->belongsTo(SystemReport::class, 'system_report_id');
    }
}
