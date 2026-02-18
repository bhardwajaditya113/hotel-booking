<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\PropertyType;
use App\Models\Amenity;
use App\Models\AmenityCategory;
use App\Models\Tag;
use App\Models\Facility;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SearchController extends Controller
{
    /**
     * Advanced search page
     */
    public function index(Request $request)
    {
        $amenityCategories = AmenityCategory::active()
            ->with('activeAmenities')
            ->ordered()
            ->get();
        
        $tags = Tag::active()->ordered()->get();
        $roomTypes = RoomType::all();
        $propertyTypes = PropertyType::all();
        $facilities = Facility::all();
        
        // Price range
        $priceRange = [
            'min' => Room::active()->min('price') ?? 0,
            'max' => Room::active()->max('price') ?? 50000,
        ];
        
        return view('frontend.search.index', compact(
            'amenityCategories', 'tags', 'roomTypes', 'propertyTypes', 'facilities', 'priceRange'
        ));
    }

    /**
     * Unified search for both hotels (OYO) and unique stays (Airbnb)
     * Supports searching by properties or rooms
     */
    public function search(Request $request)
    {
        // Determine search mode: 'properties' or 'rooms' (default: rooms for backward compatibility)
        $searchMode = $request->get('search_mode', 'rooms');
        
        if ($searchMode === 'properties') {
            return $this->searchProperties($request);
        }
        
        return $this->searchRooms($request);
    }

    /**
     * Search rooms (existing functionality)
     */
    private function searchRooms(Request $request)
    {
        $query = Room::with(['type', 'reviews', 'amenities', 'tags', 'multiImages', 'property.type', 'property.host'])
            ->active();

        // Property type filter (hotel, home, apartment, etc.)
        if ($request->property_type) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('property_type_id', $request->property_type);
            });
        }

        // Property city filter
        if ($request->property_city) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->property_city . '%');
            });
        }

        // Property name filter
        if ($request->property_name) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->property_name . '%');
            });
        }
        
        // Date availability
        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            $query->whereHas('room_numbers', function ($q) use ($checkIn, $checkOut) {
                $q->whereDoesntHave('bookedDates', function ($q2) use ($checkIn, $checkOut) {
                    $q2->whereBetween('booking_date', [$checkIn, $checkOut->subDay()]);
                });
            });
        }
        
        // Guest capacity
        if ($request->guests) {
            $query->where('total_adult', '>=', $request->guests);
        }
        
        // Room type
        if ($request->room_type) {
            $query->where('roomtype_id', $request->room_type);
        }
        
        // Price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Amenities filter
        if ($request->amenities && is_array($request->amenities)) {
            foreach ($request->amenities as $amenityId) {
                $query->whereHas('amenities', function ($q) use ($amenityId) {
                    $q->where('amenity_id', $amenityId);
                });
            }
        }
        
        // Tags filter
        if ($request->tags && is_array($request->tags)) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->whereIn('tag_id', $request->tags);
            });
        }
        
        // Rating filter
        if ($request->min_rating) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->approved()
                  ->groupBy('room_id')
                  ->havingRaw('AVG(overall_rating) >= ?', [$request->min_rating]);
            });
        }
        
        // Features
        if ($request->instant_book) {
            $query->where('instant_book', true);
        }
        if ($request->free_cancellation) {
            $query->whereHas('cancellationPolicy', function ($q) {
                $q->where('is_free_cancellation', true);
            });
        }
        if ($request->breakfast_included) {
            $query->whereHas('amenities', function ($q) {
                $q->where('slug', 'breakfast-included');
            });
        }
        
        // Sorting
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews as avg_rating', 'overall_rating')
                      ->orderBy('avg_rating', 'desc');
                break;
            case 'reviews':
                $query->withCount('reviews')
                      ->orderBy('reviews_count', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                // Featured/recommended
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('created_at', 'desc');
        }
        
        $rooms = $query->paginate(12);
        
        // Add computed properties
        $rooms->getCollection()->transform(function ($room) use ($request) {
            $room->calculated_price = null;
            if ($request->check_in && $request->check_out) {
                $room->calculated_price = $room->calculatePrice(
                    $request->check_in,
                    $request->check_out,
                    $request->guests ?? 1
                );
            }
            $room->is_wishlisted = auth()->check() 
                ? auth()->user()->hasInWishlist($room->id) 
                : false;
            return $room;
        });
        
        if ($request->ajax()) {
            return response()->json([
                'rooms' => $rooms->items(),
                'pagination' => [
                    'current_page' => $rooms->currentPage(),
                    'last_page' => $rooms->lastPage(),
                    'total' => $rooms->total(),
                    'per_page' => $rooms->perPage(),
                ],
                'html' => view('frontend.search.partials.room-cards', compact('rooms'))->render(),
            ]);
        }
        
        return view('frontend.search.results', compact('rooms'));
    }

    /**
     * Search properties (hotels and unique stays)
     */
    private function searchProperties(Request $request)
    {
        $query = Property::with(['type', 'host.hostProfile', 'activeRooms'])
            ->active();
        
        // Only filter by verified if explicitly requested
        if ($request->verified_only) {
            $query->verified();
        }

        // Listing type filter (hotel vs unique_stay)
        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

        // Property type filter
        if ($request->property_type) {
            $query->where('property_type_id', $request->property_type);
        }

        // City filter
        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // State filter
        if ($request->state) {
            $query->where('state', 'like', '%' . $request->state . '%');
        }

        // Country filter
        if ($request->country) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        // Location-based search (latitude/longitude)
        if ($request->latitude && $request->longitude) {
            $radius = $request->radius ?? 10; // Default 10km radius
            $query->nearLocation($request->latitude, $request->longitude, $radius);
        }

        // Date availability (check if any room in property is available)
        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            $query->whereHas('activeRooms.room_numbers', function ($q) use ($checkIn, $checkOut) {
                $q->whereDoesntHave('bookedDates', function ($q2) use ($checkIn, $checkOut) {
                    $q2->whereBetween('booking_date', [$checkIn, $checkOut->subDay()]);
                });
            });
        }

        // Guest capacity
        if ($request->guests) {
            $query->whereHas('activeRooms', function ($q) use ($request) {
                $q->where('total_adult', '>=', $request->guests);
            });
        }

        // Price range (based on rooms in property)
        if ($request->min_price || $request->max_price) {
            $query->whereHas('activeRooms', function ($q) use ($request) {
                if ($request->min_price) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->max_price) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

        // Amenities filter
        if ($request->amenities && is_array($request->amenities)) {
            $query->where(function($q) use ($request) {
                // Property-level amenities
                foreach ($request->amenities as $amenityId) {
                    $q->orWhereJsonContains('amenities', $amenityId);
                }
                // Or room-level amenities
                $q->orWhereHas('activeRooms.amenities', function ($q2) use ($request) {
                    $q2->whereIn('amenity_id', $request->amenities);
                });
            });
        }

        // Rating filter
        if ($request->min_rating) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        // Instant book filter
        if ($request->instant_book) {
            $query->where('instant_book_enabled', true);
        }

        // Featured properties
        if ($request->featured) {
            $query->featured();
        }

        // Verified properties only
        if ($request->verified_only) {
            $query->verified();
        }

        // Sorting
        switch ($request->sort) {
            case 'price_low':
                $query->withMin('activeRooms', 'price')
                      ->orderBy('active_rooms_min_price', 'asc');
                break;
            case 'price_high':
                $query->withMax('activeRooms', 'price')
                      ->orderBy('active_rooms_max_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'reviews':
                $query->orderBy('total_reviews', 'desc');
                break;
            case 'distance':
                // Already sorted by distance if location search
                break;
            default:
                // Featured/recommended
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('is_promoted', 'desc')
                      ->orderBy('average_rating', 'desc')
                      ->orderBy('created_at', 'desc');
        }

        $properties = $query->paginate(12);

        // Add computed properties
        $properties->getCollection()->transform(function ($property) use ($request) {
            $property->min_price = $property->activeRooms->min('price') ?? 0;
            $property->max_price = $property->activeRooms->max('price') ?? 0;
            $property->available_rooms_count = $property->activeRooms->count();
            
            if ($request->check_in && $request->check_out) {
                $property->available_rooms = $property->activeRooms->filter(function($room) use ($request) {
                    return $room->isAvailable($request->check_in, $request->check_out);
                });
            }
            
            $property->is_wishlisted = auth()->check() 
                ? auth()->user()->wishlists()->whereHas('items', function($q) use ($property) {
                    $q->whereHas('room', function($q2) use ($property) {
                        $q2->where('property_id', $property->id);
                    });
                })->exists()
                : false;
            
            return $property;
        });

        if ($request->ajax()) {
            return response()->json([
                'properties' => $properties->items(),
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'total' => $properties->total(),
                    'per_page' => $properties->perPage(),
                ],
                'html' => view('frontend.search.partials.property-cards', compact('properties'))->render(),
            ]);
        }

        return view('frontend.search.properties-results', compact('properties'));
    }

    /**
     * Get search suggestions (autocomplete)
     */
    public function suggestions(Request $request)
    {
        $query = $request->q;
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $suggestions = [];
        
        // Room names
        $rooms = Room::active()
            ->where('room_name', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'room_name', 'price']);
        
        foreach ($rooms as $room) {
            $suggestions[] = [
                'type' => 'room',
                'id' => $room->id,
                'name' => $room->room_name,
                'subtitle' => '₹' . number_format($room->price) . '/night',
                'url' => route('room.details', $room->id),
            ];
        }
        
        // Room types
        $types = RoomType::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get();
        
        foreach ($types as $type) {
            $suggestions[] = [
                'type' => 'category',
                'id' => $type->id,
                'name' => $type->name,
                'subtitle' => 'Room Category',
                'url' => route('search', ['room_type' => $type->id]),
            ];
        }
        
        // Tags
        $tags = Tag::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get();
        
        foreach ($tags as $tag) {
            $suggestions[] = [
                'type' => 'tag',
                'id' => $tag->id,
                'name' => $tag->name,
                'subtitle' => 'Popular Tag',
                'url' => route('search', ['tags' => [$tag->id]]),
            ];
        }
        
        return response()->json($suggestions);
    }

    /**
     * Quick search from homepage
     */
    public function quickSearch(Request $request)
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);
        
        return redirect()->route('search', $validated);
    }

    /**
     * Get filter counts for active filters
     */
    public function filterCounts(Request $request)
    {
        $baseQuery = Room::active();
        
        // Apply base filters
        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            
            $baseQuery->whereHas('room_numbers', function ($q) use ($checkIn, $checkOut) {
                $q->whereDoesntHave('bookedDates', function ($q2) use ($checkIn, $checkOut) {
                    $q2->whereBetween('booking_date', [$checkIn, $checkOut->subDay()]);
                });
            });
        }
        
        // Count by room type
        $typeCounts = [];
        foreach (RoomType::all() as $type) {
            $typeCounts[$type->id] = (clone $baseQuery)
                ->where('roomtype_id', $type->id)
                ->count();
        }
        
        // Count by amenity
        $amenityCounts = [];
        foreach (Amenity::active()->get() as $amenity) {
            $amenityCounts[$amenity->id] = (clone $baseQuery)
                ->whereHas('amenities', function ($q) use ($amenity) {
                    $q->where('amenity_id', $amenity->id);
                })
                ->count();
        }
        
        // Count by price range
        $priceCounts = [
            'under_2000' => (clone $baseQuery)->where('price', '<', 2000)->count(),
            '2000_5000' => (clone $baseQuery)->whereBetween('price', [2000, 5000])->count(),
            '5000_10000' => (clone $baseQuery)->whereBetween('price', [5000, 10000])->count(),
            'above_10000' => (clone $baseQuery)->where('price', '>', 10000)->count(),
        ];
        
        // Count by rating
        $ratingCounts = [];
        for ($rating = 3; $rating <= 5; $rating++) {
            $ratingCounts[$rating] = (clone $baseQuery)
                ->whereHas('reviews', function ($q) use ($rating) {
                    $q->approved()
                      ->groupBy('room_id')
                      ->havingRaw('AVG(overall_rating) >= ?', [$rating]);
                })
                ->count();
        }
        
        return response()->json([
            'types' => $typeCounts,
            'amenities' => $amenityCounts,
            'prices' => $priceCounts,
            'ratings' => $ratingCounts,
            'total' => $baseQuery->count(),
        ]);
    }

    /**
     * Compare rooms
     */
    public function compare(Request $request)
    {
        $roomIds = $request->rooms;
        
        if (!$roomIds || count($roomIds) < 2 || count($roomIds) > 4) {
            return back()->with('error', 'Please select 2-4 rooms to compare');
        }
        
        $rooms = Room::with([
            'type', 
            'amenities.category', 
            'reviews', 
            'cancellationPolicy',
            'houseRules',
        ])->whereIn('id', $roomIds)->get();
        
        // Get all amenities for comparison
        $allAmenities = Amenity::active()
            ->whereHas('rooms', function ($q) use ($roomIds) {
                $q->whereIn('room_id', $roomIds);
            })
            ->with('category')
            ->get()
            ->groupBy('category.name');
        
        return view('frontend.search.compare', compact('rooms', 'allAmenities'));
    }

    /**
     * Map view of rooms
     */
    public function mapView(Request $request)
    {
        $rooms = Room::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['type', 'reviews'])
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->room_name,
                    'price' => $room->price,
                    'formatted_price' => $room->formatted_price,
                    'rating' => round($room->average_rating, 1),
                    'review_count' => $room->review_count,
                    'image' => asset($room->image),
                    'lat' => $room->latitude,
                    'lng' => $room->longitude,
                    'url' => route('room.details', $room->id),
                ];
            });
        
        return view('frontend.search.map', compact('rooms'));
    }
}
