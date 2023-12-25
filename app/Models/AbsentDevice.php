<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsentDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'publish_topic',
        'subscribe_topic',
        'branch',
        'building',
        'room',
        'created_at',
        'updated_at'
    ];

    public function absent_log()
    {
        return $this->hasMany(AbsentLog::class);
    }

    public function absent_last_log()
    {
        return $this->hasOne(AbsentLastLog::class);
    }
}
