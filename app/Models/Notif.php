<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function absent_device()
    {
        return $this->belongsTo(AbsentDevice::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
