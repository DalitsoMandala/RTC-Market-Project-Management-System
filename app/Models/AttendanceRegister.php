<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRegister extends Model
{
    use HasFactory;

    protected $table = 'attendance_registers';
    protected $guarded = [];
}