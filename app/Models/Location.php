<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'code',
        'company_name',
        'name',
        'address',
        'city',
        'coordinate',
        'is_active',
        'last_updated_at',
        'last_updated_by',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function devices()
    {
        return $this->hasMany(Device::class, 'room', 'code');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Parse stored "lat,lng" string into array.
     * Returns null if not set, wrong format, or 0,0 / 0,1.
     */
    public function getParsedCoordinateAttribute(): ?array
    {
        if (empty($this->coordinate)) return null;

        $parts = array_map('trim', explode(',', $this->coordinate));
        if (count($parts) !== 2) return null;

        $lat = (float) $parts[0];
        $lng = (float) $parts[1];

        if (($lat == 0 && $lng == 0) || ($lat == 0 && $lng == 1)) return null;

        return ['lat' => $lat, 'lng' => $lng];
    }

    // -------------------------------------------------------------------------
    // Audit stamp — call this in controller on every store/update
    // -------------------------------------------------------------------------

    public function stampUpdatedBy(int $userId): void
    {
        $this->last_updated_by = $userId;
        $this->last_updated_at = now();
    }
}
