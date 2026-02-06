<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with(['hotel', 'permissions', 'staff']);

        if ($request->has('hotel_id')) {
            $query->byHotel($request->hotel_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $positions = $query->paginate($perPage);

        return response()->json(['success' => true, 'data' => $positions], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'nullable|exists:hotels,hotel_id',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|integer|min:1|max:10',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $position = Position::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Position created successfully', 'data' => $position->load('permissions')], 201);
    }

    public function show($id)
    {
        $position = Position::with(['hotel', 'permissions', 'staff'])->find($id);

        if (!$position) {
            return response()->json(['success' => false, 'message' => 'Position not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $position], 200);
    }

    public function update(Request $request, $id)
    {
        $position = Position::find($id);

        if (!$position) {
            return response()->json(['success' => false, 'message' => 'Position not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'hotel_id' => 'nullable|exists:hotels,hotel_id',
            'code' => 'nullable|string|max:50',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|integer|min:1|max:10',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $position->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Position updated successfully', 'data' => $position->load('permissions')], 200);
    }

    public function destroy($id)
    {
        $position = Position::find($id);

        if (!$position) {
            return response()->json(['success' => false, 'message' => 'Position not found'], 404);
        }

        $position->delete();

        return response()->json(['success' => true, 'message' => 'Position deleted successfully'], 200);
    }

    public function assignPermissions(Request $request, $id)
    {
        $position = Position::find($id);

        if (!$position) {
            return response()->json(['success' => false, 'message' => 'Position not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,permission_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $position->permissions()->sync($request->permission_ids);

        return response()->json(['success' => true, 'message' => 'Permissions assigned successfully', 'data' => $position->load('permissions')], 200);
    }
}
