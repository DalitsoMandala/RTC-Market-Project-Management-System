<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LargeCache extends Model
{
    use HasFactory;

    protected $table = 'large_cache';
    protected $fillable = ['key', 'value'];
}
