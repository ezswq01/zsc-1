<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class DeviceStatus extends Model
{
    use HasEagerLimit;

    protected $table = 'device_status';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'device_id',
        'status_type_id',
        'device_log_id',
        'user_id',
        'marked_as_read',
        'notes',
        'created_at',
        'updated_at'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function status_type()
    {
        return $this->belongsTo(StatusType::class, 'status_type_id', 'id');
    }

    public function device_log()
    {
        return $this->belongsTo(DeviceLog::class, 'device_log_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
