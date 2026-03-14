<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsentReceivedLog extends Model
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
        'marked_as_read',
        'notes',
        'created_at',
        'updated_at'
    ];

    public function absent_device()
    {
        return $this->belongsTo(AbsentDevice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'value', 'user_code');
    }
}
