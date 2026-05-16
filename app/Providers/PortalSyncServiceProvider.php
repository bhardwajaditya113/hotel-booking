<?php

namespace App\Providers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BookArea;
use App\Models\Booking;
use App\Models\BookingRoomList;
use App\Models\CancellationPolicy;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Coupon;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\HostProfile;
use App\Models\LoyaltyReward;
use App\Models\Message;
use App\Models\MultiImage;
use App\Models\Notification;
use App\Models\PricingRule;
use App\Models\Property;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomBookedDate;
use App\Models\RoomNumber;
use App\Models\RoomType;
use App\Models\SiteSetting;
use App\Models\Team;
use App\Models\Testimonial;
use App\Support\PortalSync;
use Illuminate\Support\ServiceProvider;

/**
 * Bumps {@see PortalSync::version()} when persisted data changes that can affect
 * guest/host/admin UIs. Intentionally excludes {@see Notification}
 * (user_notifications): frequent read/unread updates would spam reloads.
 */
class PortalSyncServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $sync = static function (string $source): void {
            PortalSync::bump($source);
        };

        foreach (
            [
                // Core commerce & inventory
                Booking::class => 'booking',
                BookingRoomList::class => 'booking_room_list',
                Property::class => 'property',
                Room::class => 'room',
                RoomBookedDate::class => 'room_booked_date',
                RoomNumber::class => 'room_number',
                RoomType::class => 'room_type',
                MultiImage::class => 'multi_image',
                Facility::class => 'facility',
                Review::class => 'review',
                SiteSetting::class => 'site_setting',
                Coupon::class => 'coupon',
                PricingRule::class => 'pricing_rule',
                LoyaltyReward::class => 'loyalty_reward',
                CancellationPolicy::class => 'cancellation_policy',
                // Messaging
                Conversation::class => 'conversation',
                Message::class => 'message',
                // CMS & marketing shown on the public site
                BlogPost::class => 'blog_post',
                BlogCategory::class => 'blog_category',
                Comment::class => 'comment',
                Testimonial::class => 'testimonial',
                Gallery::class => 'gallery',
                BookArea::class => 'book_area',
                Team::class => 'team',
                // Host profile (badges / trust on listings)
                HostProfile::class => 'host_profile',
            ] as $model => $tag
        ) {
            $model::saved(static fn () => $sync($tag));
            $model::deleted(static fn () => $sync($tag));
        }
    }
}
