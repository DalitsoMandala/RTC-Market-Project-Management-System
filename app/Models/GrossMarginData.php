<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrossMarginData extends Model
{
    use HasFactory;
    protected $guarded= ['id'];
    protected $table = 'gross_margin_data';

    public function grossMarginDetail()
    {
        return $this->belongsTo(GrossMarginDetail::class,'gross_margin_detail_id');
    }
}
