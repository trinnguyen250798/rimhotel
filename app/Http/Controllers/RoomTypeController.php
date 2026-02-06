<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of room types.
     */
    public function index(Request $request)
    {
        $query = RoomType::with(['hotel', 'rooms']);

        // Filter by hotel_id
        if ($request->has('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or code
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $roomTypes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $roomTypes
        ], 200);
    }

    /**
     * Get room types by hotel ID.
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

        $roomTypes = RoomType::with(['rooms'])
            ->where('hotel_id', $hotelId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roomTypes
        ], 200);
    }

    /**
     * Store a newly created room type.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,hotel_id',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'bed_type' => 'nullable|string|max:100',
            'area' => 'nullable|integer|min:1',
            'max_adult' => 'nullable|integer|min:0',
            'max_child' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user owns the hotel (for customers)
        $user = $request->user();
        $hotel = Hotel::find($request->hotel_id);

        if ($user->role === 'customer' && $hotel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only create room types for your own hotels'
            ], 403);
        }

        $roomType = RoomType::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room type created successfully',
            'data' => $roomType->load(['hotel', 'rooms'])
        ], 201);
    }

    /**
     * Display the specified room type.
     */
    public function show($id)
    {
        $roomType = RoomType::with(['hotel', 'rooms'])->find($id);

        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $roomType
        ], 200);
    }

    /**
     * Update the specified room type.
     */
    public function update(Request $request, $id)
    {
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        // Check if user owns the hotel (for customers)
        $user = $request->user();
        $hotel = $roomType->hotel;

        if ($user->role === 'customer' && $hotel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this room type'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'hotel_id' => 'sometimes|required|exists:hotels,hotel_id',
            'code' => 'nullable|string|max:50',
            'name' => 'sometimes|required|string|max:255',
            'bed_type' => 'nullable|string|max:100',
            'area' => 'nullable|integer|min:1',
            'max_adult' => 'nullable|integer|min:0',
            'max_child' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If changing hotel_id, check ownership
        if ($request->has('hotel_id') && $request->hotel_id != $roomType->hotel_id) {
            $newHotel = Hotel::find($request->hotel_id);
            if ($user->role === 'customer' && $newHotel->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only move room types to your own hotels'
                ], 403);
            }
        }

        $roomType->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room type updated successfully',
            'data' => $roomType->load(['hotel', 'rooms'])
        ], 200);
    }

    /**
     * Remove the specified room type.
     */
    public function destroy(Request $request, $id)
    {
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        // Check if user owns the hotel (for customers)
        $user = $request->user();
        $hotel = $roomType->hotel;

        if ($user->role === 'customer' && $hotel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this room type'
            ], 403);
        }

        $roomType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room type deleted successfully'
        ], 200);
    }
}
