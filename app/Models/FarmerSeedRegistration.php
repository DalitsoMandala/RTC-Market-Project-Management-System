<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmerSeedRegistration extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table ='farmer_seed_registrations';
}