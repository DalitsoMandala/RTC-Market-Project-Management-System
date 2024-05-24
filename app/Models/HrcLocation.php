<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrcLocation extends Model
{
    use HasFactory;
    protected $table = 'hrc_locations';
    protected $guarded = [];
}
