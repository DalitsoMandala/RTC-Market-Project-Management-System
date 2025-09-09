<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RootTuberSubmission extends Model
{
    use HasFactory;

    protected $table = 'root_tuber_submissions';

    protected $guarded = [];
        public function user()
    {
        return $this->belongsTo(User::class, 'submitted_user_id');
    }
}
