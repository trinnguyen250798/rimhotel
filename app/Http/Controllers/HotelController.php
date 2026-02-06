<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    /**
     * Display a listing of hotels.
     */
    public function index(Request $request)
    {
        $query = Hotel::with(['user', 'roomTypes']);

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filter by star rating
        if ($request->has('star_rating')) {
            $query->where('star_rating', $request->star_rating);
        }

        // Filter by user_id (for admin to see specific user's hotels)
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by hotel name
        if ($request->has('search')) {
            $query->where('hotel_name', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $hotels = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $hotels
        ], 200);
    }

    /**
     * Store a newly created hotel.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'star_rating' => 'nullable|integer|min:0|max:5',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_map_url' => 'nullable|url',
            'distance_to_center' => 'nullable|numeric|min:0',
            'company_name' => 'nullable|string|max:255',
            'tax_code' => 'nullable|string|max:255',
            'license_no' => 'nullable|string|max:255',
            'checkin_time' => 'nullable|date_format:H:i',
            'checkout_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'policies' => 'nullable|string',
            'languages' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $hotelData = $validator->validated();
        $hotelData['user_id'] = $request->user()->id;

        $hotel = Hotel::create($hotelData);

        return response()->json([
            'success' => true,
            'message' => 'Hotel created successfully',
            'data' => $hotel->load(['user', 'roomTypes'])
        ], 201);
    }

    /**
     * Display the specified hotel.
     */
    public function show($id)
    {
        $hotel = Hotel::with(['user', 'roomTypes'])->find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hotel
        ], 200);
    }

    /**
     * Update the specified hotel.
     */
    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found'
            ], 404);
        }

        // Check if user owns this hotel or is admin/staff
        $user = $request->user();
        if ($hotel->user_id !== $user->id && !in_array($user->role, ['root', 'admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this hotel'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'hotel_name' => 'sometimes|required|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'star_rating' => 'nullable|integer|min:0|max:5',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_map_url' => 'nullable|url',
            'distance_to_center' => 'nullable|numeric|min:0',
            'company_name' => 'nullable|string|max:255',
            'tax_code' => 'nullable|string|max:255',
            'license_no' => 'nullable|string|max:255',
            'checkin_time' => 'nullable|date_format:H:i',
            'checkout_time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'policies' => 'nullable|string',
            'languages' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $hotel->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Hotel updated successfully',
            'data' => $hotel->load(['user', 'roomTypes'])
        ], 200);
    }

    /**
     * Remove the specified hotel.
     */
    public function destroy(Request $request, $id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found'
            ], 404);
        }

        // Check if user owns this hotel or is admin/staff
        $user = $request->user();
        if ($hotel->user_id !== $user->id && !in_array($user->role, ['root', 'admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this hotel'
            ], 403);
        }

        $hotel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hotel deleted successfully'
        ], 200);
    }

    /**
     * Get hotels owned by the authenticated user.
     */
    public function myHotels(Request $request)
    {
        $hotels = Hotel::with(['roomTypes'])
            ->where('user_id', $request->user()->id)
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $hotels
        ], 200);
    }
}
