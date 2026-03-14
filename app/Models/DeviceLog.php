<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    // use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'device_id',
        'user_id',
        'value',
        'type',
        'created_at',
        'updated_at'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function device_status()
    {
        return $this->hasOne(DeviceStatus::class, 'device_log_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cam_payloads()
    {
        return $this->hasMany(CamPayload::class, 'device_log_id');
    }
}
