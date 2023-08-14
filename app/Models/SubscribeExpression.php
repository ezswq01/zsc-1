<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribeExpression extends Model
{
    // use HasFactory;

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function status_type()
    {
        return $this->belongsTo(StatusType::class, 'status_type_id', 'id');
    }
}
