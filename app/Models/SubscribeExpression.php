<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribeExpression extends Model
{
    // use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'device_id',
        'status_type_id',
        'normal_state',
        'expression',
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
}
