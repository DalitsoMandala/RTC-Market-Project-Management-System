<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketDataSubmission extends Model
{
    use HasFactory;

    protected $table = 'marketing_data_submissions';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'submitted_user_id');
    }
}
