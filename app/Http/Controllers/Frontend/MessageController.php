<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get all conversations for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $conversations = Conversation::where(function($q) use ($user) {
            $q->where('user1_id', $user->id)
              ->orWhere('user2_id', $user->id);
        })
        ->with(['user1', 'user2', 'property', 'latestMessage'])
        ->when($request->archived, function($q) use ($user) {
            if ($user->id == $q->getQuery()->wheres[0]['value']) {
                $q->where('user1_archived', true);
            } else {
                $q->where('user2_archived', true);
            }
        }, function($q) use ($user) {
            if ($user->id == $q->getQuery()->wheres[0]['value']) {
                $q->where('user1_archived', false);
            } else {
                $q->where('user2_archived', false);
            }
        })
        ->orderBy('last_message_at', 'desc')
        ->paginate(20);

        // Add unread counts
        $conversations->getCollection()->transform(function($conversation) use ($user) {
            $conversation->unread_count = $conversation->getUnreadCountFor($user->id);
            $conversation->other_user = $conversation->getOtherUser($user->id);
            return $conversation;
        });

        return view('frontend.messages.index', compact('conversations'));
    }

    /**
     * Show a specific conversation
     */
    public function show($conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::where(function($q) use ($user, $conversationId) {
            $q->where('id', $conversationId)
              ->where(function($q2) use ($user) {
                  $q2->where('user1_id', $user->id)
                     ->orWhere('user2_id', $user->id);
              });
        })
        ->with(['user1', 'user2', 'property', 'booking'])
        ->firstOrFail();

        // Mark all messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $messages = $conversation->messages()->with(['sender', 'receiver'])->get();
        $otherUser = $conversation->getOtherUser($user->id);

        return view('frontend.messages.show', compact('conversation', 'messages', 'otherUser'));
    }

    /**
     * Show form to start a new conversation
     */
    public function startConversationForm(Request $request)
    {
        $user = Auth::user();
        
        $receiverId = $request->get('receiver_id');
        $propertyId = $request->get('property_id');
        $bookingId = $request->get('booking_id');
        
        if (!$receiverId) {
            return back()->with('error', 'Invalid receiver ID.');
        }
        
        $receiver = \App\Models\User::findOrFail($receiverId);
        $property = $propertyId ? \App\Models\Property::find($propertyId) : null;
        $booking = $bookingId ? \App\Models\Booking::find($bookingId) : null;
        
        // Check if conversation already exists
        $existingConversation = Conversation::where(function($q) use ($user, $receiverId, $propertyId) {
            $q->where(function($q2) use ($user, $receiverId) {
                $q2->where('user1_id', $user->id)
                   ->where('user2_id', $receiverId);
            })->orWhere(function($q2) use ($user, $receiverId) {
                $q2->where('user1_id', $receiverId)
                   ->where('user2_id', $user->id);
            });
            if ($propertyId) {
                $q->where('property_id', $propertyId);
            }
        })->first();
        
        if ($existingConversation) {
            return redirect()->route('messages.show', $existingConversation->id);
        }
        
        return view('frontend.messages.start', compact('receiver', 'property', 'booking'));
    }

    /**
     * Start a new conversation or get existing one
     */
    public function startConversation(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'message' => 'required|string|max:5000',
        ]);

        if ($user->id == $request->receiver_id) {
            return back()->with('error', 'You cannot message yourself.');
        }

        // Get or create conversation
        $conversation = Conversation::getOrCreate(
            $user->id,
            $request->receiver_id,
            $request->property_id,
            $request->booking_id
        );

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'property_id' => $request->property_id,
            'booking_id' => $request->booking_id,
            'message' => $request->message,
        ]);

        // Update conversation
        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('messages.show', $conversation->id)
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Send a message in an existing conversation
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $user = Auth::user();
        
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $conversation = Conversation::where(function($q) use ($user, $conversationId) {
            $q->where('id', $conversationId)
              ->where(function($q2) use ($user) {
                  $q2->where('user1_id', $user->id)
                     ->orWhere('user2_id', $user->id);
              });
        })->firstOrFail();

        $receiverId = $conversation->getOtherUser($user->id)->id;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'property_id' => $conversation->property_id,
            'booking_id' => $conversation->booking_id,
            'message' => $request->message,
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load(['sender', 'receiver']),
            ]);
        }

        return back()->with('success', 'Message sent successfully.');
    }

    /**
     * Archive/unarchive a conversation
     */
    public function toggleArchive($conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::where(function($q) use ($user, $conversationId) {
            $q->where('id', $conversationId)
              ->where(function($q2) use ($user) {
                  $q2->where('user1_id', $user->id)
                     ->orWhere('user2_id', $user->id);
              });
        })->firstOrFail();

        if ($conversation->isArchivedFor($user->id)) {
            $conversation->unarchiveFor($user->id);
            $message = 'Conversation unarchived.';
        } else {
            $conversation->archiveFor($user->id);
            $message = 'Conversation archived.';
        }

        return back()->with('success', $message);
    }

    /**
     * Get unread messages count (for notifications)
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unread_messages_count;
        
        return response()->json(['count' => $count]);
    }
}

