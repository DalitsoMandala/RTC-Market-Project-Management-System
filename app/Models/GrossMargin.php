<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMargin extends Model
{
    use HasFactory;
         protected $guarded=['id'];
      public function details()
    {
        return $this->hasMany(GrossMarginDetail::class);
    }
}
