<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Mark as read
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
        return $this;
    }

    // Mark as unread
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
        return $this;
    }

    // Check if unread
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    // Get time ago
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Get icon based on type
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'booking' => 'fa-calendar-check',
            'payment' => 'fa-credit-card',
            'review' => 'fa-star',
            'loyalty' => 'fa-award',
            'promotion' => 'fa-tag',
            'user' => 'fa-user',
            'system' => 'fa-bell',
            default => 'fa-bell',
        };
    }

    // Get badge color based on type
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'booking' => 'primary',
            'payment' => 'success',
            'review' => 'warning',
            'loyalty' => 'info',
            'promotion' => 'danger',
            'user' => 'secondary',
            'system' => 'dark',
            default => 'secondary',
        };
    }

    // Send notification through various channels
    public static function send($userId, $templateSlug, $data = [], $bookingId = null)
    {
        $template = NotificationTemplate::active()->bySlug($templateSlug)->first();
        
        if (!$template) {
            \Log::warning("Notification template not found: {$templateSlug}");
            return null;
        }

        $notification = self::create([
            'user_id' => $userId,
            'template_id' => $template->id,
            'booking_id' => $bookingId,
            'type' => $template->type,
            'title' => $template->compileSubject($data),
            'body' => $template->compileBody($data),
            'data' => $data,
        ]);

        // Queue additional channels (email, sms)
        if (in_array('email', $template->channels ?? [])) {
            // Dispatch email job
            // SendNotificationEmail::dispatch($notification);
        }

        if (in_array('sms', $template->channels ?? [])) {
            // Dispatch SMS job
            // SendNotificationSms::dispatch($notification, $template->compileSmsBody($data));
        }

        return $notification;
    }

    // Mark all as read for user
    public static function markAllAsRead($userId)
    {
        return self::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    // Get unread count for user
    public static function unreadCount($userId)
    {
        return self::where('user_id', $userId)->unread()->count();
    }
}
