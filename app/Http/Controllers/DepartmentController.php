<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $query = Department::with(['hotel', 'manager', 'staff']);

        // Filter by hotel_id
        if ($request->has('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $departments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $departments
        ], 200);
    }

    /**
     * Get departments by hotel ID.
     */
    public function getByHotel($hotelId)
    {
        $hotel = Hotel::find($hotelId);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found'
            ], 404);
        }

        $departments = Department::with(['manager', 'staff'])
            ->where('hotel_id', $hotelId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $departments
        ], 200);
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,hotel_id',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_staff_id' => 'nullable|exists:staff,staff_id',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $department = Department::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully',
            'data' => $department->load(['hotel', 'manager'])
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show($id)
    {
        $department = Department::with(['hotel', 'manager', 'staff'])->find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $department
        ], 200);
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'hotel_id' => 'sometimes|required|exists:hotels,hotel_id',
            'code' => 'nullable|string|max:50',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'manager_staff_id' => 'nullable|exists:staff,staff_id',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $department->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
            'data' => $department->load(['hotel', 'manager', 'staff'])
        ], 200);
    }

    /**
     * Assign manager to department.
     */
    public function assignManager(Request $request, $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'manager_staff_id' => 'required|exists:staff,staff_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $department->update(['manager_staff_id' => $request->manager_staff_id]);

        return response()->json([
            'success' => true,
            'message' => 'Manager assigned successfully',
            'data' => $department->load(['manager'])
        ], 200);
    }

    /**
     * Remove the specified department.
     */
    public function destroy($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully'
        ], 200);
    }
}
