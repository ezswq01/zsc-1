<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsentLastLog extends Model
{
    use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'absent_device_id',
        'absent_log_id',
        'status',
        'value',
        'created_at',
        'updated_at'
    ];
}
