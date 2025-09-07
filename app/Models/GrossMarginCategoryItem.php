<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMarginCategoryItem extends Model
{
    use HasFactory;
        protected $guarded = [];
    public function category()
    {
        return $this->belongsTo(GrossMarginCategory::class, 'gross_margin_category_id');
    }

    public function grossItems()
    {
        return $this->hasMany(GrossMarginItemValue::class, 'gross_margin_category_item_id');
    }
}
