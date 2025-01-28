<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsiblePerson extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'responsible_people';
    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }
    public function sources()
    {
        return $this->hasMany(Source::class, 'person_id');
    }
}
