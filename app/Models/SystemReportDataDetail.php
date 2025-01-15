<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemReportDataDetail extends Model
{
    use HasFactory;

    protected $table = 'system_report_data_details';
    protected $guarded = ['id'];

    public function systemReport()
    {
        return $this->belongsTo(SystemReport::class, 'system_report_id');
    }
}
