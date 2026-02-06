<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $primaryKey = 'position_id';

    protected $fillable = [
        'hotel_id',
        'code',
        'name',
        'description',
        'level',
        'status',
    ];

    /**
     * Get the hotel that owns the position (nullable for global positions).
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    /**
     * Get all staff with this position.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'position_id', 'position_id');
    }

    /**
     * Get all permissions for this position.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'position_permissions',
            'position_id',
            'permission_id'
        );
    }

    /**
     * Check if position has a specific permission.
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Scope a query to only include active positions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to filter by hotel (including global positions).
     */
    public function scopeByHotel($query, $hotelId)
    {
        return $query->where(function ($q) use ($hotelId) {
            $q->where('hotel_id', $hotelId)
                ->orWhereNull('hotel_id');
        });
    }

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'status' => 'integer',
        ];
    }
}
