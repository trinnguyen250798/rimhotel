<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffGroup extends Model
{
    protected $primaryKey = 'staff_group_id';

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'color',
    ];

    /**
     * Get the hotel that owns the staff group.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    /**
     * Get all staff in this group.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'staff_group_id', 'staff_group_id');
    }

    /**
     * Scope a query to filter by hotel.
     */
    public function scopeByHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }
}
