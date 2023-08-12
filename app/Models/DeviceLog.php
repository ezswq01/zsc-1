<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    // use HasFactory;

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function device_status()
    {
        return $this->hasOne(DeviceStatus::class, 'device_log_id');
    }
}
