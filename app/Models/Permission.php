<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
    ];

    /**
     * Get all positions that have this permission.
     */
    public function positions()
    {
        return $this->belongsToMany(
            Position::class,
            'position_permissions',
            'permission_id',
            'position_id'
        );
    }

    /**
     * Get all staff that have this permission.
     */
    public function staff()
    {
        return $this->belongsToMany(
            Staff::class,
            'staff_permissions',
            'permission_id',
            'staff_id'
        )->withPivot('granted');
    }

    /**
     * Scope a query to filter by module.
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }
}
