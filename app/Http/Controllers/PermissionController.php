<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->has('module')) {
            $query->where('module', $request->module);
        }

        $permissions = $query->get();

        return response()->json(['success' => true, 'data' => $permissions], 200);
    }

    public function getByModule($module)
    {
        $permissions = Permission::where('module', $module)->get();

        return response()->json(['success' => true, 'data' => $permissions], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $permission = Permission::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Permission created successfully', 'data' => $permission], 201);
    }

    public function show($id)
    {
        $permission = Permission::with(['positions', 'staff'])->find($id);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $permission], 200);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|unique:permissions,name,' . $id . ',permission_id|max:255',
            'display_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $permission->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Permission updated successfully', 'data' => $permission], 200);
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
        }

        $permission->delete();

        return response()->json(['success' => true, 'message' => 'Permission deleted successfully'], 200);
    }
}
