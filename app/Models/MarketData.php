<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketData extends Model
{
    use HasFactory;

    protected $table = 'marketing_data';
    protected $guarded = ['id'];
    public function submissions()
    {
        return $this->belongsTo(MarketDataSubmission::class, 'submission_id');
    }

        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

}
