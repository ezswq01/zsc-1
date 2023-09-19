<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    // use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'name'
    ];

    public function regency()
    {
        return $this->hasMany(Regency::class, 'province_id');
    }
}
