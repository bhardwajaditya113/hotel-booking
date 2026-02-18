<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'last_message_at' => 'datetime',
        'user1_archived' => 'boolean',
        'user2_archived' => 'boolean',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function getOtherUser($userId)
    {
        return $this->user1_id == $userId ? $this->user2 : $this->user1;
    }

    public function isArchivedFor($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user1_archived;
        }
        return $this->user2_archived;
    }

    public function archiveFor($userId)
    {
        if ($this->user1_id == $userId) {
            $this->update(['user1_archived' => true]);
        } else {
            $this->update(['user2_archived' => true]);
        }
    }

    public function unarchiveFor($userId)
    {
        if ($this->user1_id == $userId) {
            $this->update(['user1_archived' => false]);
        } else {
            $this->update(['user2_archived' => false]);
        }
    }

    public function getUnreadCountFor($userId)
    {
        return $this->messages()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public static function getOrCreate($user1Id, $user2Id, $propertyId = null, $bookingId = null)
    {
        // Ensure user1_id is always smaller for consistency
        if ($user1Id > $user2Id) {
            [$user1Id, $user2Id] = [$user2Id, $user1Id];
        }

        return static::firstOrCreate(
            [
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
                'property_id' => $propertyId,
            ],
            [
                'booking_id' => $bookingId,
            ]
        );
    }
}


