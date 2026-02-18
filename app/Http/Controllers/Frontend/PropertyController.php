<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\HostProfile;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    // Show property registration form
    public function create()
    {
        $types = PropertyType::all();
        return view('frontend.property.create', compact('types'));
    }

    // Show property details
    public function show($id)
    {
        $property = Property::with(['type', 'host.hostProfile', 'activeRooms.reviews'])
            ->findOrFail($id);
        
        $property->incrementViewCount();
        
        return view('frontend.property.show', compact('property'));
    }

    // Edit property
    public function edit($id)
    {
        $property = Property::where('user_id', Auth::id())->findOrFail($id);
        $types = PropertyType::all();
        return view('frontend.property.edit', compact('property', 'types'));
    }

    // Update property
    public function update(Request $request, $id)
    {
        $property = Property::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'property_type_id' => 'required|exists:property_types,id',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'nullable|string',
            'country' => 'required|string',
            'zipcode' => 'nullable|string',
            'description' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'instant_book_enabled' => 'boolean',
        ]);

        $property->update([
            'name' => $request->name,
            'property_type_id' => $request->property_type_id,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'zipcode' => $request->zipcode,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'instant_book_enabled' => $request->has('instant_book_enabled'),
        ]);

        return redirect()->route('property.dashboard')
            ->with('success', 'Property updated successfully!');
    }

    // Store new property
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'property_type_id' => 'required|exists:property_types,id',
            'listing_type' => 'required|in:hotel,unique_stay',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'nullable|string',
            'country' => 'required|string',
            'zipcode' => 'nullable|string',
            'description' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'instant_book_enabled' => 'boolean',
        ]);

        $property = Property::create([
            'name' => $request->name,
            'property_type_id' => $request->property_type_id,
            'listing_type' => $request->listing_type,
            'user_id' => Auth::id(),
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country ?? 'India',
            'zipcode' => $request->zipcode,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'instant_book_enabled' => $request->has('instant_book_enabled'),
            'status' => 'active',
            'verification_status' => 'pending',
        ]);

        // Create or update host profile
        $hostProfile = HostProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'display_name' => Auth::user()->name,
                'type' => $request->listing_type === 'hotel' ? 'company' : 'individual',
                'verification_status' => 'pending',
            ]
        );

        return redirect()->route('property.dashboard')
            ->with('success', 'Property created successfully! It will be reviewed for verification.');
    }

    // Show host dashboard (listings)
    public function dashboard()
    {
        $properties = Property::where('user_id', Auth::id())
            ->with(['type', 'activeRooms'])
            ->latest()
            ->get();
        
        $hostProfile = HostProfile::where('user_id', Auth::id())->first();
        
        // Statistics
        $stats = [
            'total_properties' => $properties->count(),
            'active_properties' => $properties->where('status', 'active')->count(),
            'pending_verification' => $properties->where('verification_status', 'pending')->count(),
            'total_bookings' => \App\Models\Booking::whereIn('property_id', $properties->pluck('id'))->count(),
            'total_revenue' => \App\Models\Booking::whereIn('property_id', $properties->pluck('id'))
                ->where('status', 1)
                ->sum('total_price'),
        ];
        
        return view('frontend.property.dashboard', compact('properties', 'hostProfile', 'stats'));
    }

    // Store property booking
    public function storeBooking(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'persion' => 'required|integer|min:1',
            'number_of_rooms' => 'required|integer|min:1',
        ]);

        $property = Property::findOrFail($request->property_id);
        $room = \App\Models\Room::findOrFail($request->room_id);

        // Verify room belongs to property
        if ($room->property_id != $property->id) {
            return back()->with('error', 'Invalid room selection.');
        }

        // Check room availability
        $checkIn = \Carbon\Carbon::parse($request->check_in);
        $checkOut = \Carbon\Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        // Check if room is available for the dates
        $bookedDates = \App\Models\RoomBookedDate::where('room_id', $room->id)
            ->whereBetween('book_date', [$checkIn->format('Y-m-d'), $checkOut->subDay()->format('Y-m-d')])
            ->count();

        if ($bookedDates > 0) {
            return back()->with('error', 'Room is not available for the selected dates.');
        }

        // Calculate pricing
        $subtotal = $room->price * $nights * $request->number_of_rooms;
        $discount = 0; // Can add discount logic here
        $total_price = $subtotal - $discount;

        // Store booking data in session for checkout
        $book_data = [
            'property_id' => $property->id,
            'room_id' => $room->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'persion' => $request->persion,
            'number_of_rooms' => $request->number_of_rooms,
            'nights' => $nights,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total_price' => $total_price,
        ];

        \Illuminate\Support\Facades\Session::put('book_date', $book_data);

        return redirect()->route('checkout');
    }
}
