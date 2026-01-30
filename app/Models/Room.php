<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $primaryKey = 'room_id';

    protected $fillable = [
        'room_type_id',
        'room_no',
        'floor',
        'view',
        'status',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'room_type_id');
    }

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'status' => 'integer',
        ];
    }
}
