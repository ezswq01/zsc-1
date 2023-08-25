<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusTypeWidget extends Model
{
    // use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'setting_id',
        'status_type_id',
        'created_at',
        'updated_at'
    ];

    public function setting()
    {
        return $this->belongsTo(Setting::class, 'setting_id', 'id');
    }

    public function status_type()
    {
        return $this->belongsTo(StatusType::class, 'status_type_id', 'id');
    }
}
