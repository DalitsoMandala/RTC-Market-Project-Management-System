<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobProgress extends Model
{
    use HasFactory;
    protected $table = 'job_progress';

    protected $fillable = ['cache_key', 'total_rows', 'processed_rows', 'progress', 'status', 'user_id', 'form_name', 'error'];
}
