<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusType extends Model
{
    // use HasFactory;

    public function device_status()
    {
        return $this->hasMany(DeviceStatus::class, 'status_type_id');
    }

    public function subscribe_expression()
    {
        return $this->hasMany(SubscribeExpression::class, 'status_type_id');
    }
}
