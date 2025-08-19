<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMarginCategory extends Model
{
    use HasFactory;
    public function categoryItems()
    {
        return $this->hasMany(GrossMarginCategoryItem::class, 'gross_margin_category_id');
    }
}
