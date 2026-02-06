<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'hotel_id',
        'code',
        'name',
        'description',
        'manager_staff_id',
        'status',
    ];

    /**
     * Get the hotel that owns the department.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    /**
     * Get all staff in this department.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'department_id', 'department_id');
    }

    /**
     * Get the manager of this department.
     */
    public function manager()
    {
        return $this->belongsTo(Staff::class, 'manager_staff_id', 'staff_id');
    }

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to filter by hotel.
     */
    public function scopeByHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }
}
