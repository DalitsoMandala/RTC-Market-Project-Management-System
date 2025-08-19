<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMarginCategoryItem extends Model
{
    use HasFactory;
    public function category()
    {
        return $this->belongsTo(GrossMarginCategory::class, 'gross_margin_category_id');
    }
}
