<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewPhoto;
use App\Models\ReviewHelpful;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ReviewController extends Controller
{
    /**
     * Display reviews for a room
     */
    public function index($roomId)
    {
        $room = Room::findOrFail($roomId);
        
        $reviews = $room->approvedReviews()
            ->with(['user', 'photos', 'booking'])
            ->withCount('helpfulVotes')
            ->paginate(10);
        
        $ratingBreakdown = $room->rating_breakdown;
        
        return view('frontend.reviews.index', compact('room', 'reviews', 'ratingBreakdown'));
    }

    /**
     * Show form to create a review
     */
    public function create($bookingId)
    {
        $booking = Booking::with('room')->findOrFail($bookingId);
        
        // Check if user can review this booking
        if (!Auth::user()->canReviewBooking($booking)) {
            return back()->with('error', 'You cannot review this booking');
        }
        
        return view('frontend.reviews.create', compact('booking'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::with('room')->findOrFail($bookingId);
        
        // Validate user can review
        if (!Auth::user()->canReviewBooking($booking)) {
            return back()->with('error', 'You cannot review this booking');
        }
        
        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'location_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'amenities_rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'required|string|min:20|max:2000',
            'pros' => 'nullable|string|max:500',
            'cons' => 'nullable|string|max:500',
            'trip_type' => 'nullable|in:business,leisure,family,couple,solo',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        // Create review
        $review = Review::create([
            'user_id' => Auth::id(),
            'room_id' => $booking->rooms_id,
            'booking_id' => $booking->id,
            'overall_rating' => $validated['overall_rating'],
            'cleanliness_rating' => $validated['cleanliness_rating'] ?? $validated['overall_rating'],
            'location_rating' => $validated['location_rating'] ?? $validated['overall_rating'],
            'value_rating' => $validated['value_rating'] ?? $validated['overall_rating'],
            'service_rating' => $validated['service_rating'] ?? $validated['overall_rating'],
            'amenities_rating' => $validated['amenities_rating'] ?? $validated['overall_rating'],
            'title' => $validated['title'],
            'review' => $validated['review'],
            'pros' => $validated['pros'],
            'cons' => $validated['cons'],
            'trip_type' => $validated['trip_type'],
            'stay_date' => $booking->check_in,
            'status' => 'pending', // Auto-approve can be configured
        ]);
        
        // Handle photos
        if ($request->hasFile('photos')) {
            $order = 0;
            foreach ($request->file('photos') as $photo) {
                $filename = 'review_' . $review->id . '_' . time() . '_' . $order . '.' . $photo->extension();
                $path = 'upload/reviews/' . $filename;
                
                // Resize and save
                Image::make($photo)->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save(public_path($path));
                
                ReviewPhoto::create([
                    'review_id' => $review->id,
                    'photo' => $path,
                    'sort_order' => $order++,
                ]);
            }
        }
        
        // Award bonus loyalty points for review
        $userLoyalty = Auth::user()->getOrCreateLoyalty();
        if ($userLoyalty) {
            $bonusPoints = 50; // Bonus for leaving a review
            if (count($request->file('photos') ?? []) > 0) {
                $bonusPoints += 25; // Extra for photos
            }
            $userLoyalty->addPoints($bonusPoints, 'review', "Bonus for leaving a review");
        }
        
        return redirect()->route('user.bookings')
            ->with('success', 'Thank you for your review! It will be published after moderation.');
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        
        // Check if already voted
        $existing = ReviewHelpful::where('review_id', $reviewId)
            ->where('user_id', Auth::id())
            ->first();
        
        if ($existing) {
            // Toggle vote
            $existing->delete();
            $message = 'Vote removed';
        } else {
            ReviewHelpful::create([
                'review_id' => $reviewId,
                'user_id' => Auth::id(),
            ]);
            $message = 'Marked as helpful';
        }
        
        $count = $review->helpfulVotes()->count();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
            ]);
        }
        
        return back()->with('success', $message);
    }

    /**
     * Get reviews for AJAX loading
     */
    public function getReviews(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);
        
        $query = $room->approvedReviews()
            ->with(['user', 'photos'])
            ->withCount('helpfulVotes');
        
        // Filter by rating
        if ($request->rating) {
            $query->where('overall_rating', $request->rating);
        }
        
        // Filter by trip type
        if ($request->trip_type) {
            $query->where('trip_type', $request->trip_type);
        }
        
        // Sort
        switch ($request->sort) {
            case 'newest':
                $query->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'highest':
                $query->orderBy('overall_rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('overall_rating', 'asc');
                break;
            case 'helpful':
                $query->orderBy('helpful_votes_count', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $reviews = $query->paginate(5);
        
        return response()->json([
            'reviews' => $reviews->items(),
            'hasMore' => $reviews->hasMorePages(),
            'nextPage' => $reviews->currentPage() + 1,
        ]);
    }

    /**
     * User's reviews list
     */
    public function myReviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with(['room', 'photos'])
            ->latest()
            ->paginate(10);
        
        return view('frontend.reviews.my-reviews', compact('reviews'));
    }

    /**
     * Load more reviews (AJAX pagination)
     */
    public function loadMore(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);
        
        $reviews = $room->approvedReviews()
            ->with(['user', 'photos', 'booking'])
            ->withCount('helpfulVotes')
            ->latest()
            ->paginate(5, ['*'], 'page', $request->page ?? 1);

        return view('frontend.partials.review-items', compact('reviews'));
    }

    /**
     * Report a review
     */
    public function report(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        
        // Check if already reported by this user
        $alreadyReported = \DB::table('review_reports')
            ->where('review_id', $reviewId)
            ->where('user_id', Auth::id())
            ->exists();
        
        if ($alreadyReported) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this review'
            ]);
        }
        
        // Record the report
        \DB::table('review_reports')->insert([
            'review_id' => $reviewId,
            'user_id' => Auth::id(),
            'reason' => $request->reason ?? 'inappropriate',
            'created_at' => now(),
        ]);
        
        // Mark review for moderation if multiple reports
        $reportCount = \DB::table('review_reports')
            ->where('review_id', $reviewId)
            ->count();
        
        if ($reportCount >= 3) {
            $review->update(['status' => 'flagged']);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Review reported. Our team will review it shortly.'
        ]);
    }
}
