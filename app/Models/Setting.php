<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'app_name',
        'logo',
        'is_access_device',
        'created_at',
        'updated_at'
    ];

    public function status_type_widget()
    {
        return $this->hasMany(StatusTypeWidget::class, 'setting_id');
    }
}
