<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms.
     */
    public function index(Request $request)
    {
        $query = Room::with(['roomType.hotel']);

        // Filter by room_type_id
        if ($request->has('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by floor
        if ($request->has('floor')) {
            $query->where('floor', $request->floor);
        }

        // Filter by view
        if ($request->has('view')) {
            $query->where('view', 'like', '%' . $request->view . '%');
        }

        // Search by room number
        if ($request->has('search')) {
            $query->where('room_no', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $rooms = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $rooms
        ], 200);
    }

    /**
     * Get rooms by room type ID.
     */
    public function getByRoomType($roomTypeId)
    {
        $roomType = RoomType::find($roomTypeId);

        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        $rooms = Room::where('room_type_id', $roomTypeId)->get();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ], 200);
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_type_id' => 'required|exists:room_types,room_type_id',
            'room_no' => 'required|string|max:50',
            'floor' => 'nullable|integer|min:0',
            'view' => 'nullable|string|max:255',
            'status' => 'nullable|integer|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if room_no already exists for this room type
        $existingRoom = Room::where('room_type_id', $request->room_type_id)
            ->where('room_no', $request->room_no)
            ->first();

        if ($existingRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Room number already exists for this room type'
            ], 422);
        }

        // Check if user owns the hotel (for customers)
        $user = $request->user();
        $roomType = RoomType::with('hotel')->find($request->room_type_id);

        if ($user->role === 'customer' && $roomType->hotel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only create rooms for your own hotels'
            ], 403);
        }

        $room = Room::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully',
            'data' => $room->load(['roomType.hotel'])
        ], 201);
    }

    /**
     * Display the specified room.
     */
    public function show($id)
    {
        $room = Room::with(['roomType.hotel'])->find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $room
        ], 200);
    }

    /**
     * Update the specified room.
     */
    public function update(Request $request, $id)
    {
        $room = Room::with('roomType.hotel')->find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        // Check if user owns the hotel (for customers)
        $user = $request->user();
        $hotel = $room->roomType->hotel;

        if ($user->role === 'customer' && $hotel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this room'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'room_type_id' => 'sometimes|required|exists:room_types,room_type_id',
            'room_no' => 'sometimes|required|string|max:50',
            'floor' => 'nullable|integer|min:0',
            'view' => 'nullable|string|max:255',
            'status' => 'nullable|integer|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if room_no already exists (if changing)
        if ($request->has('room_no') && $request->room_no !== $room->room_no) {
            $roomTypeId = $request->room_type_id ?? $room->room_type_id;
            $existingRoom = Room::where('room_type_id', $roomTypeId)
                ->where('room_no', $request->room_no)
                ->where('room_id', '!=', $id)
                ->first();

            if ($existingRoom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room number already exists for this room type'
                ], 422);
            }
        }

        // If changing room_type_id, check ownership
        if ($request->has('room_type_id') && $request->room_type_id != $room->room_type_id) {
            $newRoomType = RoomType::with('hotel')->find($request->room_type_id);
            if ($user->role === 'customer' && $newRoomType->hotel->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only move rooms to your own hotels'
                ], 403);
            }
        }

        $room->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room->load(['roomType.hotel'])
        ], 200);
    }

    /**
     * Update room status.
     */
    public function updateStatus(Request $request, $id)
    {
        $room = Room::with('roomType.hotel')->find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        // Check if user owns the hotel (for customers)
        $user = $request->user();
        $hotel = $room->roomType->hotel;

        if ($user->role === 'customer' && $hotel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this room status'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $room->update(['status' => $request->status]);

        $statusText = ['Trống', 'Bận', 'Sửa chữa'][$request->status];

        return response()->json([
            'success' => true,
            'message' => "Room status updated to: {$statusText}",
            'data' => $room->load(['roomType.hotel'])
        ], 200);
    }

    /**
     * Remove the specified room.
     */
    public function destroy(Request $request, $id)
    {
        $room = Room::with('roomType.hotel')->find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        // Check if user owns the hotel (for customers)
        $user = $request->user();
        $hotel = $room->roomType->hotel;

        if ($user->role === 'customer' && $hotel->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this room'
            ], 403);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully'
        ], 200);
    }
}
