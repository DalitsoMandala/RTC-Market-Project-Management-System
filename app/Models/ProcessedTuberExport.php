<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedTuberExport extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'processed_tuber_exports';
}
