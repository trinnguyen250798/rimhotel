<?php

namespace App\Http\Controllers;

use App\Models\StaffGroup;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffGroup::with(['hotel', 'staff']);

        if ($request->has('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        $perPage = $request->get('per_page', 15);
        $groups = $query->paginate($perPage);

        return response()->json(['success' => true, 'data' => $groups], 200);
    }

    public function getByHotel($hotelId)
    {
        $hotel = Hotel::find($hotelId);

        if (!$hotel) {
            return response()->json(['success' => false, 'message' => 'Hotel not found'], 404);
        }

        $groups = StaffGroup::with('staff')->where('hotel_id', $hotelId)->get();

        return response()->json(['success' => true, 'data' => $groups], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,hotel_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $group = StaffGroup::create($validator->validated());

        return response()->json(['success' => true, 'message' => 'Staff group created successfully', 'data' => $group], 201);
    }

    public function show($id)
    {
        $group = StaffGroup::with(['hotel', 'staff'])->find($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Staff group not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $group], 200);
    }

    public function update(Request $request, $id)
    {
        $group = StaffGroup::find($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Staff group not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'hotel_id' => 'sometimes|required|exists:hotels,hotel_id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $group->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Staff group updated successfully', 'data' => $group], 200);
    }

    public function destroy($id)
    {
        $group = StaffGroup::find($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Staff group not found'], 404);
        }

        $group->delete();

        return response()->json(['success' => true, 'message' => 'Staff group deleted successfully'], 200);
    }
}
