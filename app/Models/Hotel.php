<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $primaryKey = 'hotel_id';

    protected $fillable = [
        'user_id',
        'hotel_name',
        'city',
        'district',
        'ward',
        'website',
        'star_rating',
        'latitude',
        'longitude',
        'google_map_url',
        'distance_to_center',
        'company_name',
        'tax_code',
        'license_no',
        'checkin_time',
        'checkout_time',
        'description',
        'amenities',
        'policies',
        'languages',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class, 'hotel_id', 'hotel_id');
    }

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'languages' => 'array',
            'star_rating' => 'integer',
        ];
    }
}
