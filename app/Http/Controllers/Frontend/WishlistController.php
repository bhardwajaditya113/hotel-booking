<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\WishlistShare;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    /**
     * Display user's wishlists
     */
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->withCount('items')
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('frontend.wishlists.index', compact('wishlists'));
    }

    /**
     * Show a specific wishlist
     */
    public function show($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())
            ->with(['items.room.reviews', 'items.room.type'])
            ->findOrFail($id);
        
        return view('frontend.wishlists.show', compact('wishlist'));
    }

    /**
     * Create a new wishlist
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);
        
        $wishlist = Wishlist::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
            'is_default' => false,
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'wishlist' => $wishlist,
                'message' => 'Wishlist created successfully',
            ]);
        }
        
        return redirect()->route('wishlists.show', $wishlist->id)
            ->with('success', 'Wishlist created successfully');
    }

    /**
     * Update wishlist
     */
    public function update(Request $request, $id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);
        
        $wishlist->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Wishlist updated successfully',
            ]);
        }
        
        return back()->with('success', 'Wishlist updated successfully');
    }

    /**
     * Delete wishlist
     */
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->findOrFail($id);
        
        // Don't allow deleting default wishlist
        if ($wishlist->is_default) {
            return back()->with('error', 'Cannot delete default wishlist');
        }
        
        $wishlist->delete();
        
        return redirect()->route('wishlists.index')
            ->with('success', 'Wishlist deleted successfully');
    }

    /**
     * Add room to wishlist
     */
    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'wishlist_id' => 'nullable|exists:wishlists,id',
        ]);
        
        $room = Room::findOrFail($validated['room_id']);
        
        // Get or create wishlist
        if ($validated['wishlist_id']) {
            $wishlist = Wishlist::where('user_id', Auth::id())
                ->findOrFail($validated['wishlist_id']);
        } else {
            $wishlist = Auth::user()->getOrCreateDefaultWishlist();
        }
        
        // Check if already in wishlist
        $existing = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('room_id', $room->id)
            ->first();
        
        if ($existing) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room already in wishlist',
                ]);
            }
            return back()->with('info', 'Room already in wishlist');
        }
        
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'room_id' => $room->id,
            'notes' => $request->notes,
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to wishlist',
                'wishlist_count' => $wishlist->items()->count(),
            ]);
        }
        
        return back()->with('success', 'Added to wishlist');
    }

    /**
     * Remove room from wishlist
     */
    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'wishlist_id' => 'nullable|exists:wishlists,id',
        ]);
        
        $query = WishlistItem::where('room_id', $validated['room_id'])
            ->whereHas('wishlist', function ($q) {
                $q->where('user_id', Auth::id());
            });
        
        if ($validated['wishlist_id']) {
            $query->where('wishlist_id', $validated['wishlist_id']);
        }
        
        $deleted = $query->delete();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => $deleted > 0,
                'message' => $deleted > 0 ? 'Removed from wishlist' : 'Item not found',
            ]);
        }
        
        return back()->with('success', 'Removed from wishlist');
    }

    /**
     * Toggle room in wishlist (add/remove)
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);
        
        $wishlist = Auth::user()->getOrCreateDefaultWishlist();
        
        $existing = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('room_id', $validated['room_id'])
            ->first();
        
        if ($existing) {
            $existing->delete();
            $added = false;
            $message = 'Removed from wishlist';
        } else {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'room_id' => $validated['room_id'],
            ]);
            $added = true;
            $message = 'Added to wishlist';
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $message,
            ]);
        }
        
        return back()->with('success', $message);
    }

    /**
     * Share wishlist
     */
    public function share(Request $request, $id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500',
        ]);
        
        $token = Str::random(32);
        
        $share = WishlistShare::create([
            'wishlist_id' => $wishlist->id,
            'shared_by' => Auth::id(),
            'shared_with_email' => $validated['email'],
            'share_token' => $token,
            'message' => $validated['message'],
            'expires_at' => now()->addDays(30),
        ]);
        
        // TODO: Send email with share link
        // Mail::to($validated['email'])->send(new WishlistShared($share));
        
        $shareUrl = route('wishlists.shared', $token);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'share_url' => $shareUrl,
                'message' => 'Wishlist shared successfully',
            ]);
        }
        
        return back()->with('success', 'Wishlist shared successfully');
    }

    /**
     * View shared wishlist
     */
    public function viewShared($token)
    {
        $share = WishlistShare::where('share_token', $token)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();
        
        // Mark as viewed
        if (!$share->viewed_at) {
            $share->update(['viewed_at' => now()]);
        }
        
        $wishlist = $share->wishlist()
            ->with(['items.room.reviews', 'items.room.type', 'user'])
            ->first();
        
        return view('frontend.wishlists.shared', compact('wishlist', 'share'));
    }

    /**
     * Get user's wishlists for dropdown
     */
    public function getWishlists(Request $request)
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->select('id', 'name', 'is_default')
            ->withCount('items')
            ->orderBy('is_default', 'desc')
            ->get();
        
        return response()->json([
            'wishlists' => $wishlists,
        ]);
    }

    /**
     * Move item between wishlists
     */
    public function moveItem(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:wishlist_items,id',
            'to_wishlist_id' => 'required|exists:wishlists,id',
        ]);
        
        $item = WishlistItem::whereHas('wishlist', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($validated['item_id']);
        
        $toWishlist = Wishlist::where('user_id', Auth::id())
            ->findOrFail($validated['to_wishlist_id']);
        
        // Check if already exists in target wishlist
        $exists = WishlistItem::where('wishlist_id', $toWishlist->id)
            ->where('room_id', $item->room_id)
            ->exists();
        
        if ($exists) {
            $item->delete(); // Remove from current list
            $message = 'Item already exists in target wishlist, removed from current';
        } else {
            $item->update(['wishlist_id' => $toWishlist->id]);
            $message = 'Item moved successfully';
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }
        
        return back()->with('success', $message);
    }
}
