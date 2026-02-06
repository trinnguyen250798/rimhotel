<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Hotel;
use App\Models\Department;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with(['hotel', 'user', 'department', 'position', 'staffGroup']);

        if ($request->has('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_code', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->get('per_page', 15);
        $staff = $query->paginate($perPage);

        return response()->json(['success' => true, 'data' => $staff], 200);
    }

    public function getByHotel($hotelId)
    {
        $hotel = Hotel::find($hotelId);

        if (!$hotel) {
            return response()->json(['success' => false, 'message' => 'Hotel not found'], 404);
        }

        $staff = Staff::with(['department', 'position', 'staffGroup'])
            ->where('hotel_id', $hotelId)
            ->get();

        return response()->json(['success' => true, 'data' => $staff], 200);
    }

    public function getByDepartment($departmentId)
    {
        $department = Department::find($departmentId);

        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }

        $staff = Staff::with(['position', 'staffGroup'])
            ->where('department_id', $departmentId)
            ->get();

        return response()->json(['success' => true, 'data' => $staff], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,hotel_id',
            'user_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,department_id',
            'position_id' => 'nullable|exists:positions,position_id',
            'staff_group_id' => 'nullable|exists:staff_groups,staff_group_id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'employee_code' => 'nullable|string|max:50|unique:staff,employee_code',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:full_time,part_time,contract,intern',
            'salary' => 'nullable|numeric|min:0',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'status' => 'nullable|integer|in:0,1,2',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $staff = Staff::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Staff created successfully', 'data' => $staff->load(['hotel', 'department', 'position', 'staffGroup'])], 201);
    }

    public function show($id)
    {
        $staff = Staff::with(['hotel', 'user', 'department', 'position', 'staffGroup', 'permissions'])->find($id);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found'], 404);
        }

        // Include all permissions (from position + staff-specific)
        $allPermissions = $staff->getAllPermissions();

        $data = $staff->toArray();
        $data['all_permissions'] = $allPermissions;

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'hotel_id' => 'sometimes|required|exists:hotels,hotel_id',
            'user_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,department_id',
            'position_id' => 'nullable|exists:positions,position_id',
            'staff_group_id' => 'nullable|exists:staff_groups,staff_group_id',
            'full_name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:staff,email,' . $id . ',staff_id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'employee_code' => 'nullable|string|max:50|unique:staff,employee_code,' . $id . ',staff_id',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:full_time,part_time,contract,intern',
            'salary' => 'nullable|numeric|min:0',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'status' => 'nullable|integer|in:0,1,2',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $staff->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Staff updated successfully', 'data' => $staff->load(['hotel', 'department', 'position', 'staffGroup'])], 200);
    }

    public function destroy($id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found'], 404);
        }

        $staff->delete();

        return response()->json(['success' => true, 'message' => 'Staff deleted successfully'], 200);
    }

    public function grantPermission(Request $request, $id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,permission_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $staff->grantPermission($request->permission_id);

        return response()->json(['success' => true, 'message' => 'Permission granted successfully', 'data' => $staff->load('permissions')], 200);
    }

    public function revokePermission($staffId, $permissionId)
    {
        $staff = Staff::find($staffId);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found'], 404);
        }

        $permission = Permission::find($permissionId);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
        }

        $staff->revokePermission($permissionId);

        return response()->json(['success' => true, 'message' => 'Permission revoked successfully', 'data' => $staff->load('permissions')], 200);
    }
}
