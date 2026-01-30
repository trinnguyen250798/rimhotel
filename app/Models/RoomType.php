<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $primaryKey = 'room_type_id';

    protected $fillable = [
        'hotel_id',
        'code',
        'name',
        'bed_type',
        'area',
        'max_adult',
        'max_child',
        'description',
        'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id', 'room_type_id');
    }

    protected function casts(): array
    {
        return [
            'area' => 'integer',
            'max_adult' => 'integer',
            'max_child' => 'integer',
            'status' => 'integer',
        ];
    }
}
