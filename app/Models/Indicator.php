<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function responsiblePeople()
    {
        return $this->hasMany(ResponsiblePerson::class, 'indicator_id');
    }

    public function forms()
{
    return $this->belongsToMany(Form::class, 'indicator_form');
}

}