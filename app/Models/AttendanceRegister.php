<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRegister extends Model
{
    use HasFactory;

    protected $table = 'attendance_registers';
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Sequential numeric ID format
            $att = AttendanceRegister::latest('id')->first();
            $number = $att ? $att->id + 1 : 1; // Increment based on the latest ID
            $model->att_id = 'ATT-' . str_pad($number, 5, '0', STR_PAD_LEFT); // Example: FARM-00001
        });
    }

    /**
     * Get the user that owns the AttendanceRegister
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
