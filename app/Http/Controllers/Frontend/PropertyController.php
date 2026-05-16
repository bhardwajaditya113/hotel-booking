<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Events\GuestBookingDecisionReceived;
use App\Http\Requests\StorePropertyBookingRequest;
use App\Http\Requests\StorePropertyListingRequest;
use App\Http\Requests\UpdatePropertyListingRequest;
use App\Models\AmenityCategory;
use App\Models\Booking;
use App\Models\HostProfile;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\RoomBookedDate;
use App\Support\PropertyListingNormalizer;
use Carbon\Carbon;
use App\Mail\BookConfirm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    // Show property registration form
    public function create()
    {
        $types = PropertyType::query()->orderBy('name')->get();
        $countries = config('listing_form.countries', []);
        $indianStatesUt = config('listing_form.indian_states_ut', []);
        $amenityCategories = AmenityCategory::query()
            ->active()
            ->ordered()
            ->with(['activeAmenities'])
            ->get();

        return view('frontend.property.create', compact(
            'types',
            'countries',
            'indianStatesUt',
            'amenityCategories'
        ));
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
        $types = PropertyType::query()->orderBy('name')->get();
        $countries = config('listing_form.countries', []);
        $indianStatesUt = config('listing_form.indian_states_ut', []);
        $amenityCategories = AmenityCategory::query()
            ->active()
            ->ordered()
            ->with(['activeAmenities'])
            ->get();

        return view('frontend.property.edit', compact(
            'property',
            'types',
            'countries',
            'indianStatesUt',
            'amenityCategories'
        ));
    }

    // Update property
    public function update(UpdatePropertyListingRequest $request, $id)
    {
        $property = Property::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validated();

        $amenityIds = PropertyListingNormalizer::amenityIds($validated['amenities'] ?? null);

        $updates = [
            'name' => $validated['name'],
            'property_type_id' => $validated['property_type_id'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'instant_book_enabled' => $request->boolean('instant_book_enabled'),
        ];

        foreach (['state', 'zipcode', 'description', 'phone', 'email', 'latitude', 'longitude', 'house_rules'] as $field) {
            if (array_key_exists($field, $validated)) {
                $updates[$field] = $validated[$field];
            }
        }

        if (array_key_exists('check_in_time', $validated)) {
            $fallbackIn = $property->check_in_time ? substr((string) $property->check_in_time, 0, 5) : '14:00';
            $updates['check_in_time'] = PropertyListingNormalizer::checkTime($validated['check_in_time'], $fallbackIn);
        }

        if (array_key_exists('check_out_time', $validated)) {
            $fallbackOut = $property->check_out_time ? substr((string) $property->check_out_time, 0, 5) : '11:00';
            $updates['check_out_time'] = PropertyListingNormalizer::checkTime($validated['check_out_time'], $fallbackOut);
        }

        if (array_key_exists('amenities', $validated)) {
            $updates['amenities'] = $amenityIds;
        }

        if (array_key_exists('cancellation_preset', $validated)) {
            $updates['cancellation_policy_text'] = PropertyListingNormalizer::cancellationBody(
                $validated['cancellation_preset'],
                $validated['cancellation_policy_text'] ?? null
            );
        }

        $meta = PropertyListingNormalizer::metaFields($validated);
        $updates['meta_title'] = $meta['meta_title'];
        $updates['meta_description'] = $meta['meta_description'];

        $property->update($updates);

        return redirect()->route('property.dashboard')
            ->with([
                'message' => __('frontend.host_hub.flash_updated'),
                'alert-type' => 'success',
            ]);
    }

    // Store new property
    public function store(StorePropertyListingRequest $request)
    {
        $validated = $request->validated();

        $amenityIds = PropertyListingNormalizer::amenityIds($validated['amenities'] ?? null);

        $preset = $validated['cancellation_preset'] ?? null;
        $cancellationBody = PropertyListingNormalizer::cancellationBody(
            $preset,
            $validated['cancellation_policy_text'] ?? null
        );

        $checkIn = PropertyListingNormalizer::checkTime($validated['check_in_time'] ?? null, '14:00');
        $checkOut = PropertyListingNormalizer::checkTime($validated['check_out_time'] ?? null, '11:00');

        $baseSlug = Str::slug($validated['name']);
        if ($baseSlug === '') {
            $baseSlug = 'listing-'.Str::lower(Str::random(8));
        }
        $slug = $baseSlug;
        $n = 1;
        while (Property::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$n++;
        }

        $meta = PropertyListingNormalizer::metaFields($validated);

        Property::create([
            'name' => $validated['name'],
            'property_type_id' => $validated['property_type_id'],
            'listing_type' => $validated['listing_type'],
            'user_id' => Auth::id(),
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'zipcode' => $validated['zipcode'] ?? null,
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'instant_book_enabled' => $request->boolean('instant_book_enabled'),
            'check_in_time' => $checkIn,
            'check_out_time' => $checkOut,
            'amenities' => $amenityIds,
            'house_rules' => $validated['house_rules'] ?? null,
            'cancellation_policy_text' => $cancellationBody,
            'slug' => $slug,
            'meta_title' => $meta['meta_title'],
            'meta_description' => $meta['meta_description'],
            'status' => 'active',
            'verification_status' => 'pending',
        ]);

        HostProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'display_name' => Auth::user()->name,
                'type' => $validated['listing_type'] === 'hotel' ? 'company' : 'individual',
                'verification_status' => 'pending',
            ]
        );

        return redirect()->route('property.dashboard')
            ->with([
                'message' => __('frontend.host_hub.flash_created'),
                'alert-type' => 'success',
            ]);
    }

    // Show host dashboard (listings)
    public function dashboard()
    {
        $properties = Property::where('user_id', Auth::id())
            ->with(['type', 'activeRooms'])
            ->latest()
            ->get();

        $hostProfile = HostProfile::where('user_id', Auth::id())->first();

        $propertyIds = $properties->pluck('id');

        $totalBookings = Booking::whereIn('property_id', $propertyIds)->count();
        $totalRevenue = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 1)
            ->sum('total_price');

        $stats = [
            'total_properties' => $properties->count(),
            'active_properties' => $properties->where('status', 'active')->count(),
            'pending_verification' => $properties->where('verification_status', 'pending')->count(),
            'verified_properties' => $properties->where('verification_status', 'verified')->count(),
            'pending_booking_requests' => $propertyIds->isEmpty()
                ? 0
                : Booking::whereIn('property_id', $propertyIds)->where('host_approval_status', 'pending')->count(),
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'total_room_units' => $properties->sum(fn ($p) => $p->activeRooms->count()),
        ];

        $stats['total_revenue_display'] = $totalRevenue > 0
            ? '₹'.number_format((float) $totalRevenue)
            : '—';

        return view('frontend.property.dashboard', compact('properties', 'hostProfile', 'stats'));
    }

    // Store property booking
    public function storeBooking(StorePropertyBookingRequest $request)
    {
        $validated = $request->validated();

        $property = Property::findOrFail($validated['property_id']);
        $room = Room::findOrFail($validated['room_id']);

        $checkIn = Carbon::parse($validated['check_in'])->startOfDay();
        $checkOut = Carbon::parse($validated['check_out'])->startOfDay();
        $lastBookableNight = $checkOut->copy()->subDay();
        $nights = $checkIn->diffInDays($checkOut);

        $bookedDates = RoomBookedDate::where('room_id', $room->id)
            ->whereBetween('book_date', [$checkIn->toDateString(), $lastBookableNight->toDateString()])
            ->count();

        if ($bookedDates > 0) {
            return back()->with('error', 'Room is not available for the selected dates.');
        }

        // Calculate pricing
        $subtotal = $room->price * $nights * $validated['number_of_rooms'];
        $discount = 0;
        $total_price = $subtotal - $discount;

        // Store booking data in session for checkout
        $book_data = [
            'property_id' => $property->id,
            'room_id' => $room->id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'persion' => $validated['persion'],
            'number_of_rooms' => $validated['number_of_rooms'],
            'nights' => $nights,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total_price' => $total_price,
        ];

        Session::put('book_date', $book_data);

        return redirect()->route('checkout');
    }

    /**
     * Reservations across all listings owned by the signed-in host (approve / decline requests).
     */
    public function incomingBookings()
    {
        $propertyIds = Property::where('user_id', Auth::id())->pluck('id');

        $bookings = Booking::with(['user', 'room.type', 'property'])
            ->whereIn('property_id', $propertyIds)
            ->orderByRaw("CASE WHEN host_approval_status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->paginate(25);

        return view('frontend.property.host_bookings', compact('bookings'));
    }

    public function approveBooking(Request $request, Booking $booking)
    {
        $propertyIds = Property::where('user_id', Auth::id())->pluck('id');
        abort_unless($booking->property_id && $propertyIds->contains($booking->property_id), 403);
        abort_unless($booking->host_approval_status === 'pending', 422);

        $room = Room::findOrFail($booking->rooms_id);

        Booking::attachRoomBookedNights($booking, $room);

        $booking->host_approval_status = 'approved';
        if ($booking->payment_method === 'COD') {
            $booking->status = 1;
        }
        $booking->save();

        $payUrl = ($booking->payment_method === 'Razorpay' && (int) $booking->payment_status === 0)
            ? route('razorpay.payment', ['booking_id' => $booking->id])
            : null;

        $this->broadcastGuestBookingDecision($booking, 'approved', $payUrl);

        try {
            if ($booking->payment_method === 'Razorpay' && (int) $booking->payment_status === 0) {
                Mail::raw(
                    __('frontend.host_hub.mail_booking_approved_pay_body', ['url' => $payUrl]),
                    function ($message) use ($booking) {
                        $message->to($booking->email)->subject(__('frontend.host_hub.mail_booking_approved_pay_subject'));
                    }
                );
            } else {
                Mail::to($booking->email)->send(new BookConfirm([
                    'check_in' => $booking->check_in,
                    'check_out' => $booking->check_out,
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'phone' => $booking->phone,
                    'booking_code' => $booking->code,
                    'total_price' => $booking->total_price,
                    'room' => $booking->room,
                    'property' => $booking->property,
                ]));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('property.bookings.incoming')->with([
            'message' => __('frontend.host_hub.flash_booking_approved'),
            'alert-type' => 'success',
        ]);
    }

    public function declineBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $propertyIds = Property::where('user_id', Auth::id())->pluck('id');
        abort_unless($booking->property_id && $propertyIds->contains($booking->property_id), 403);
        abort_unless($booking->host_approval_status === 'pending', 422);

        $booking->host_approval_status = 'declined';
        $booking->status = 2;
        $booking->host_decline_notes = $request->input('notes');
        $booking->save();

        $notes = $request->input('notes');

        $this->broadcastGuestBookingDecision($booking, 'declined', null, $notes);

        try {
            $body = __('frontend.host_hub.mail_booking_declined_body', ['code' => $booking->code]);
            if ($notes) {
                $body .= "\n\n".__('frontend.host_hub.mail_booking_declined_notes_label')."\n".$notes;
            }
            Mail::raw(
                $body,
                function ($message) use ($booking) {
                    $message->to($booking->email)->subject(__('frontend.host_hub.mail_booking_declined_subject'));
                }
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('property.bookings.incoming')->with([
            'message' => __('frontend.host_hub.flash_booking_declined'),
            'alert-type' => 'success',
        ]);
    }

    protected function broadcastGuestBookingDecision(Booking $booking, string $decision, ?string $payUrl = null, ?string $declineReason = null): void
    {
        if (! $booking->user_id || config('broadcasting.default') === 'null') {
            return;
        }

        try {
            broadcast(new GuestBookingDecisionReceived(
                $booking->user_id,
                $booking->id,
                (string) $booking->code,
                $decision,
                $payUrl,
                $declineReason
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
