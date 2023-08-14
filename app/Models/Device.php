<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    // use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'device_id',
        'device_type_id',
        'publish_topic',
        'subscribe_topic',
        'created_at',
        'updated_at'
    ];

    public function device_type()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function device_log()
    {
        return $this->hasMany(DeviceLog::class, 'device_id');
    }

    public function device_status()
    {
        return $this->hasOne(DeviceStatus::class, 'device_id');
    }

    public function publish_action()
    {
        return $this->hasMany(PublishAction::class, 'device_id');
    }

    public function subscribe_expression()
    {
        return $this->hasMany(SubscribeExpression::class, 'device_id');
    }
}
