<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsentLog extends Model
{
    use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'absent_device_id',
        'status',
        'value',
        'created_at',
        'updated_at'
    ];

    public function absent_device()
    {
        return $this->belongsTo(AbsentDevice::class);
    }
}
