<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cgiar_Project extends Model
{
    use HasFactory;
    protected $table = "cgiar_projects";
    protected $guarded = ['id'];
}