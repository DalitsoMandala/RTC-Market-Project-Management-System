<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function users()
    {
        return $this->hasMany(User::class, 'organisation_id');
    }

    public function indicatorResponsiblePeople()
    {
        return $this->hasMany(ResponsiblePerson::class, 'organisation_id');
    }

    /**
     * Get all of the comments for the Organisation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */

}
