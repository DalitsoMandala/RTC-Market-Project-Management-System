<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossSubmission extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'gross_submissions';
    public function user()
    {
        return $this->belongsTo(User::class,'submitted_user_id');
    }
}
