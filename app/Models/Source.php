<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function person()
    {
        return $this->belongsTo(ResponsiblePerson::class,'person_id');
    }
}