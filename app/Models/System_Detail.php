<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System_Detail extends Model
{
    use HasFactory;
    protected $table = 'system_details';
    protected $guarded = ['id'];
}