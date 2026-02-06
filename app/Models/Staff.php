<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'hotel_id',
        'user_id',
        'department_id',
        'position_id',
        'staff_group_id',
        'full_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'employee_code',
        'hire_date',
        'contract_type',
        'salary',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'status',
        'notes',
    ];

    /**
     * Get the hotel that owns the staff.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    /**
     * Get the user account linked to this staff (if any).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the department of this staff.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    /**
     * Get the position of this staff.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    /**
     * Get the staff group.
     */
    public function staffGroup()
    {
        return $this->belongsTo(StaffGroup::class, 'staff_group_id', 'staff_group_id');
    }

    /**
     * Get all permissions directly assigned to this staff.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'staff_permissions',
            'staff_id',
            'permission_id'
        )->withPivot('granted');
    }

    /**
     * Get all permissions (from position + staff-specific).
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        // Get permissions from position
        if ($this->position) {
            $positionPermissions = $this->position->permissions;
            $permissions = $permissions->merge($positionPermissions);
        }

        // Get staff-specific permissions
        $staffPermissions = $this->permissions;

        foreach ($staffPermissions as $permission) {
            if ($permission->pivot->granted) {
                // Add permission if granted
                if (!$permissions->contains('permission_id', $permission->permission_id)) {
                    $permissions->push($permission);
                }
            } else {
                // Remove permission if revoked
                $permissions = $permissions->reject(function ($p) use ($permission) {
                    return $p->permission_id === $permission->permission_id;
                });
            }
        }

        return $permissions->unique('permission_id');
    }

    /**
     * Check if staff has a specific permission.
     */
    public function hasPermission($permissionName)
    {
        return $this->getAllPermissions()->contains('name', $permissionName);
    }

    /**
     * Grant a permission to this staff.
     */
    public function grantPermission($permissionId)
    {
        return $this->permissions()->syncWithoutDetaching([
            $permissionId => ['granted' => true]
        ]);
    }

    /**
     * Revoke a permission from this staff.
     */
    public function revokePermission($permissionId)
    {
        return $this->permissions()->syncWithoutDetaching([
            $permissionId => ['granted' => false]
        ]);
    }

    /**
     * Scope a query to only include active staff.
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

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by position.
     */
    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'status' => 'integer',
        ];
    }
}
