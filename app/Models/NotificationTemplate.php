<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'channels' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Compile template with variables
    public function compileSubject($variables)
    {
        return $this->compile($this->subject, $variables);
    }

    public function compileBody($variables)
    {
        return $this->compile($this->body, $variables);
    }

    public function compileSmsBody($variables)
    {
        return $this->compile($this->sms_body, $variables);
    }

    protected function compile($template, $variables)
    {
        if (!$template) return null;
        
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        
        return $template;
    }

    // Create default templates
    public static function createDefaults()
    {
        $templates = [
            [
                'name' => 'Booking Confirmation',
                'slug' => 'booking-confirmation',
                'type' => 'booking',
                'channels' => ['email', 'database', 'sms'],
                'subject' => 'Your booking #{{booking_code}} is confirmed!',
                'body' => '<h2>Booking Confirmed!</h2><p>Dear {{guest_name}},</p><p>Your booking at {{hotel_name}} has been confirmed.</p><p><strong>Booking Details:</strong></p><ul><li>Check-in: {{check_in}}</li><li>Check-out: {{check_out}}</li><li>Room: {{room_name}}</li><li>Guests: {{guests}}</li><li>Total: {{total_amount}}</li></ul><p>We look forward to welcoming you!</p>',
                'sms_body' => 'Your booking #{{booking_code}} at {{hotel_name}} is confirmed. Check-in: {{check_in}}. Total: {{total_amount}}',
                'variables' => ['booking_code', 'guest_name', 'hotel_name', 'check_in', 'check_out', 'room_name', 'guests', 'total_amount'],
            ],
            [
                'name' => 'Booking Cancelled',
                'slug' => 'booking-cancelled',
                'type' => 'booking',
                'channels' => ['email', 'database'],
                'subject' => 'Your booking #{{booking_code}} has been cancelled',
                'body' => '<h2>Booking Cancelled</h2><p>Dear {{guest_name}},</p><p>Your booking #{{booking_code}} at {{hotel_name}} has been cancelled.</p><p><strong>Refund Details:</strong></p><p>Refund Amount: {{refund_amount}}</p><p>Refund will be processed within 5-7 business days.</p>',
                'sms_body' => 'Your booking #{{booking_code}} has been cancelled. Refund of {{refund_amount}} will be processed shortly.',
                'variables' => ['booking_code', 'guest_name', 'hotel_name', 'refund_amount'],
            ],
            [
                'name' => 'Check-in Reminder',
                'slug' => 'checkin-reminder',
                'type' => 'booking',
                'channels' => ['email', 'database', 'sms'],
                'subject' => 'Check-in tomorrow at {{hotel_name}}!',
                'body' => '<h2>Check-in Tomorrow!</h2><p>Dear {{guest_name}},</p><p>This is a reminder that your check-in at {{hotel_name}} is tomorrow.</p><p><strong>Details:</strong></p><ul><li>Check-in Time: {{check_in_time}}</li><li>Room: {{room_name}}</li><li>Booking Code: {{booking_code}}</li></ul><p>Please bring a valid ID for check-in.</p>',
                'sms_body' => 'Reminder: Check-in tomorrow at {{hotel_name}}. Time: {{check_in_time}}. Code: {{booking_code}}',
                'variables' => ['booking_code', 'guest_name', 'hotel_name', 'room_name', 'check_in_time'],
            ],
            [
                'name' => 'Review Request',
                'slug' => 'review-request',
                'type' => 'review',
                'channels' => ['email', 'database'],
                'subject' => 'How was your stay at {{hotel_name}}?',
                'body' => '<h2>We hope you enjoyed your stay!</h2><p>Dear {{guest_name}},</p><p>Thank you for choosing {{hotel_name}}. We would love to hear about your experience.</p><p>Please take a moment to leave a review:</p><p><a href="{{review_url}}" style="display:inline-block;padding:10px 20px;background:#3B82F6;color:white;text-decoration:none;border-radius:5px;">Write a Review</a></p>',
                'sms_body' => 'Thank you for staying at {{hotel_name}}! Share your experience: {{review_url}}',
                'variables' => ['guest_name', 'hotel_name', 'review_url'],
            ],
            [
                'name' => 'Payment Received',
                'slug' => 'payment-received',
                'type' => 'payment',
                'channels' => ['email', 'database'],
                'subject' => 'Payment received for booking #{{booking_code}}',
                'body' => '<h2>Payment Successful</h2><p>Dear {{guest_name}},</p><p>We have received your payment of {{amount}} for booking #{{booking_code}}.</p><p>Transaction ID: {{transaction_id}}</p>',
                'sms_body' => 'Payment of {{amount}} received for booking #{{booking_code}}. Transaction: {{transaction_id}}',
                'variables' => ['booking_code', 'guest_name', 'amount', 'transaction_id'],
            ],
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome-email',
                'type' => 'user',
                'channels' => ['email'],
                'subject' => 'Welcome to {{app_name}}!',
                'body' => '<h2>Welcome, {{user_name}}!</h2><p>Thank you for joining {{app_name}}. We are excited to have you on board.</p><p>Start exploring amazing hotels and book your next perfect stay.</p><p><a href="{{explore_url}}" style="display:inline-block;padding:10px 20px;background:#3B82F6;color:white;text-decoration:none;border-radius:5px;">Explore Hotels</a></p>',
                'sms_body' => null,
                'variables' => ['user_name', 'app_name', 'explore_url'],
            ],
            [
                'name' => 'Loyalty Points Earned',
                'slug' => 'loyalty-points-earned',
                'type' => 'loyalty',
                'channels' => ['database', 'email'],
                'subject' => 'You earned {{points}} points!',
                'body' => '<h2>Congratulations!</h2><p>Dear {{user_name}},</p><p>You have earned {{points}} loyalty points for your booking #{{booking_code}}.</p><p>Your current balance: {{total_points}} points</p>',
                'sms_body' => 'You earned {{points}} points! Total: {{total_points}}. Redeem on your next booking.',
                'variables' => ['user_name', 'points', 'booking_code', 'total_points'],
            ],
            [
                'name' => 'Tier Upgrade',
                'slug' => 'tier-upgrade',
                'type' => 'loyalty',
                'channels' => ['email', 'database'],
                'subject' => 'Congratulations! You are now {{tier_name}}!',
                'body' => '<h2>Tier Upgrade!</h2><p>Dear {{user_name}},</p><p>Congratulations! You have been upgraded to {{tier_name}} tier.</p><p>Enjoy your new benefits:</p><ul>{{benefits_list}}</ul>',
                'sms_body' => 'Congratulations! You are now {{tier_name}} member. Enjoy exclusive benefits!',
                'variables' => ['user_name', 'tier_name', 'benefits_list'],
            ],
            [
                'name' => 'Refund Processed',
                'slug' => 'refund-processed',
                'type' => 'payment',
                'channels' => ['email', 'database', 'sms'],
                'subject' => 'Refund of {{amount}} processed',
                'body' => '<h2>Refund Processed</h2><p>Dear {{guest_name}},</p><p>Your refund of {{amount}} for booking #{{booking_code}} has been processed.</p><p>The amount will be credited to your account within 5-7 business days.</p>',
                'sms_body' => 'Refund of {{amount}} for booking #{{booking_code}} processed. Will be credited in 5-7 days.',
                'variables' => ['guest_name', 'amount', 'booking_code'],
            ],
            [
                'name' => 'Price Drop Alert',
                'slug' => 'price-drop-alert',
                'type' => 'promotion',
                'channels' => ['email', 'database'],
                'subject' => 'Price dropped for {{room_name}}!',
                'body' => '<h2>Price Drop Alert!</h2><p>Dear {{user_name}},</p><p>Good news! The price for {{room_name}} at {{hotel_name}} has dropped from {{old_price}} to {{new_price}}.</p><p><a href="{{booking_url}}" style="display:inline-block;padding:10px 20px;background:#10B981;color:white;text-decoration:none;border-radius:5px;">Book Now</a></p>',
                'sms_body' => 'Price dropped! {{room_name}} now at {{new_price}}. Book now: {{booking_url}}',
                'variables' => ['user_name', 'room_name', 'hotel_name', 'old_price', 'new_price', 'booking_url'],
            ],
        ];

        foreach ($templates as $template) {
            $template['is_active'] = true;
            self::updateOrCreate(['slug' => $template['slug']], $template);
        }
    }
}
