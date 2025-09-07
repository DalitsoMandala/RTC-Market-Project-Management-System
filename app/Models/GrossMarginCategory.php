<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMarginCategory extends Model
{
    use HasFactory;
        protected $guarded = [];
    public function categoryItems()
    {
        return $this->hasMany(GrossMarginCategoryItem::class, 'gross_margin_category_id');
    }

    public function grossItems()
    {
        return $this->hasManyThrough(GrossMarginItemValue::class, GrossMarginCategoryItem::class, 'gross_margin_category_id', 'gross_margin_category_item_id');
    }
}
