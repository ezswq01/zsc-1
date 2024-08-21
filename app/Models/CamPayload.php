<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CamPayload extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_log_id',
        'file',
        'file_name',
        'created_at',
        'updated_at'
    ];
}
