<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropVariety extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'crop_id', 'name'];

    public function crop()
    {
        return $this->belongsTo(Crop::class, 'crop_id');
    }
}
