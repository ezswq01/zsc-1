<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // use HasFactory;
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'app_name',
        'mqtt_main_topic',
        'logo',
        'is_access_device',
        'created_at',
        'updated_at',
        'email_users'
    ];

    protected $casts = [
        'email_users' => 'array'
    ];

    public function status_type_widget()
    {
        return $this->hasMany(StatusTypeWidget::class, 'setting_id');
    }

    public function users()
    {
        return $this->belongsToJson(User::class, 'email_users');
    }
}
