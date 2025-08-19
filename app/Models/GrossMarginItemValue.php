<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMarginItemValue extends Model
{
    use HasFactory;
    public function categoryItem()
    {
        return $this->belongsTo(GrossMarginCategoryItem::class, 'gross_margin_category_item_id');
    }

    public function grossMargin()
    {
        return $this->belongsTo(GrossMargin::class, 'gross_margin_id');
    }
}
