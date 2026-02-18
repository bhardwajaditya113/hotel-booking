<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnhancedFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Enhanced Features...');

        $this->seedCancellationPolicies();
        $this->seedLoyaltyTiers();
        $this->seedLoyaltyRewards();
        $this->seedPaymentMethods();
        $this->seedAmenities();
        $this->seedTags();
        $this->seedHouseRules();
        $this->seedNotificationTemplates();

        $this->command->info('Enhanced Features seeded successfully!');
    }

    private function seedCancellationPolicies(): void
    {
        $this->command->info('Creating Cancellation Policies...');

        $policies = [
            [
                'name' => 'Flexible',
                'slug' => 'flexible',
                'description' => 'Free cancellation up to 24 hours before check-in',
                'rules' => json_encode([
                    'full_refund_days' => 1,
                    'full_refund_percentage' => 100,
                    'partial_refund_days' => 0,
                    'partial_refund_percentage' => 50,
                    'deadline_hours' => 24,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Moderate',
                'slug' => 'moderate',
                'description' => 'Free cancellation up to 5 days before check-in',
                'rules' => json_encode([
                    'full_refund_days' => 5,
                    'full_refund_percentage' => 100,
                    'partial_refund_days' => 1,
                    'partial_refund_percentage' => 50,
                    'deadline_hours' => 48,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Strict',
                'slug' => 'strict',
                'description' => '50% refund up to 7 days before check-in',
                'rules' => json_encode([
                    'full_refund_days' => 7,
                    'full_refund_percentage' => 50,
                    'partial_refund_days' => 3,
                    'partial_refund_percentage' => 0,
                    'deadline_hours' => 72,
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Non-Refundable',
                'slug' => 'non-refundable',
                'description' => 'This reservation is non-refundable',
                'rules' => json_encode([
                    'full_refund_days' => 0,
                    'full_refund_percentage' => 0,
                    'partial_refund_days' => 0,
                    'partial_refund_percentage' => 0,
                    'deadline_hours' => 0,
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($policies as $policy) {
            DB::table('cancellation_policies')->updateOrInsert(
                ['slug' => $policy['slug']],
                array_merge($policy, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedLoyaltyTiers(): void
    {
        $this->command->info('Creating Loyalty Tiers...');

        $tiers = [
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'min_points' => 0,
                'min_bookings' => 0,
                'points_multiplier' => 100, // 1x
                'discount_percentage' => 0,
                'benefits' => json_encode([
                    'Earn 10 points per ₹100 spent',
                    'Member-only offers',
                    'Early access to sales',
                ]),
                'color' => '#CD7F32',
                'icon' => 'medal',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'min_points' => 1000,
                'min_bookings' => 3,
                'points_multiplier' => 125, // 1.25x
                'discount_percentage' => 5,
                'benefits' => json_encode([
                    'All Bronze benefits',
                    '25% bonus points',
                    '5% discount on all bookings',
                    'Priority customer support',
                ]),
                'color' => '#C0C0C0',
                'icon' => 'award',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'min_points' => 5000,
                'min_bookings' => 10,
                'points_multiplier' => 150, // 1.5x
                'discount_percentage' => 10,
                'benefits' => json_encode([
                    'All Silver benefits',
                    '50% bonus points',
                    '10% discount on all bookings',
                    'Free breakfast',
                    'Late checkout',
                ]),
                'color' => '#FFD700',
                'icon' => 'crown',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'min_points' => 15000,
                'min_bookings' => 25,
                'points_multiplier' => 200, // 2x
                'discount_percentage' => 15,
                'benefits' => json_encode([
                    'All Gold benefits',
                    'Double points on all stays',
                    '15% discount on all bookings',
                    'Guaranteed room upgrade',
                    'Free airport transfer',
                    '24/7 dedicated concierge',
                ]),
                'color' => '#E5E4E2',
                'icon' => 'gem',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tier) {
            DB::table('loyalty_tiers')->updateOrInsert(
                ['slug' => $tier['slug']],
                array_merge($tier, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedLoyaltyRewards(): void
    {
        $this->command->info('Creating Loyalty Rewards...');

        $rewards = [
            [
                'name' => '₹100 Wallet Credit',
                'slug' => 'wallet-credit-100',
                'description' => 'Get ₹100 credited to your wallet instantly',
                'type' => 'voucher',
                'points_required' => 500,
                'value' => 100,
                'discount_percentage' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '₹250 Wallet Credit',
                'slug' => 'wallet-credit-250',
                'description' => 'Get ₹250 credited to your wallet instantly',
                'type' => 'voucher',
                'points_required' => 1000,
                'value' => 250,
                'discount_percentage' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '10% Off Coupon',
                'slug' => 'discount-10-percent',
                'description' => 'Get 10% off on your next booking',
                'type' => 'discount',
                'points_required' => 750,
                'value' => 1000,
                'discount_percentage' => 10,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => '15% Off Coupon',
                'slug' => 'discount-15-percent',
                'description' => 'Get 15% off on your next booking',
                'type' => 'discount',
                'points_required' => 1200,
                'value' => 1500,
                'discount_percentage' => 15,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Free Room Upgrade',
                'slug' => 'free-upgrade',
                'description' => 'Complimentary room upgrade on your next stay',
                'type' => 'upgrade',
                'points_required' => 1500,
                'value' => null,
                'discount_percentage' => null,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Free Night Stay',
                'slug' => 'free-night',
                'description' => 'Redeem for one free night (up to ₹3000 value)',
                'type' => 'free_night',
                'points_required' => 5000,
                'value' => 3000,
                'discount_percentage' => null,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Spa Experience',
                'slug' => 'spa-experience',
                'description' => 'Complimentary 60-minute spa treatment',
                'type' => 'experience',
                'points_required' => 2500,
                'value' => 2000,
                'discount_percentage' => null,
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($rewards as $reward) {
            DB::table('loyalty_rewards')->updateOrInsert(
                ['slug' => $reward['slug']],
                array_merge($reward, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedPaymentMethods(): void
    {
        $this->command->info('Creating Payment Methods...');

        $methods = [
            [
                'name' => 'Stripe',
                'slug' => 'stripe',
                'provider' => 'stripe',
                'description' => 'Pay securely with credit or debit card',
                'icon' => 'fa-credit-card',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Razorpay',
                'slug' => 'razorpay',
                'provider' => 'razorpay',
                'description' => 'UPI, Net Banking, Cards, Wallets',
                'icon' => 'fa-building-columns',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Wallet',
                'slug' => 'wallet',
                'provider' => 'wallet',
                'description' => 'Pay using your wallet balance',
                'icon' => 'fa-wallet',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['slug' => $method['slug']],
                array_merge($method, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedAmenities(): void
    {
        $this->command->info('Creating Amenities...');

        $categories = [
            ['name' => 'Room Essentials', 'slug' => 'room-essentials', 'icon' => 'fa-bed'],
            ['name' => 'Bathroom', 'slug' => 'bathroom', 'icon' => 'fa-bath'],
            ['name' => 'Technology', 'slug' => 'technology', 'icon' => 'fa-wifi'],
            ['name' => 'Kitchen', 'slug' => 'kitchen', 'icon' => 'fa-utensils'],
            ['name' => 'Safety', 'slug' => 'safety', 'icon' => 'fa-shield'],
        ];

        foreach ($categories as $category) {
            DB::table('amenity_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                array_merge($category, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $amenities = [
            ['name' => 'Free WiFi', 'slug' => 'free-wifi', 'icon' => 'fa-wifi', 'category_slug' => 'technology'],
            ['name' => 'Air Conditioning', 'slug' => 'air-conditioning', 'icon' => 'fa-snowflake', 'category_slug' => 'room-essentials'],
            ['name' => 'TV', 'slug' => 'tv', 'icon' => 'fa-tv', 'category_slug' => 'technology'],
            ['name' => 'Mini Bar', 'slug' => 'mini-bar', 'icon' => 'fa-wine-glass', 'category_slug' => 'room-essentials'],
            ['name' => 'Room Service', 'slug' => 'room-service', 'icon' => 'fa-bell-concierge', 'category_slug' => 'room-essentials'],
            ['name' => 'Hair Dryer', 'slug' => 'hair-dryer', 'icon' => 'fa-wind', 'category_slug' => 'bathroom'],
            ['name' => 'Safe', 'slug' => 'safe', 'icon' => 'fa-lock', 'category_slug' => 'safety'],
            ['name' => 'Coffee Maker', 'slug' => 'coffee-maker', 'icon' => 'fa-mug-hot', 'category_slug' => 'kitchen'],
        ];

        foreach ($amenities as $amenity) {
            $categoryId = DB::table('amenity_categories')->where('slug', $amenity['category_slug'])->value('id');
            unset($amenity['category_slug']);
            DB::table('amenities')->updateOrInsert(
                ['slug' => $amenity['slug']],
                array_merge($amenity, ['category_id' => $categoryId, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedTags(): void
    {
        $this->command->info('Creating Tags...');

        $tags = [
            ['name' => 'Pet Friendly', 'slug' => 'pet-friendly', 'type' => 'feature'],
            ['name' => 'Family Friendly', 'slug' => 'family-friendly', 'type' => 'feature'],
            ['name' => 'Business', 'slug' => 'business', 'type' => 'style'],
            ['name' => 'Romantic', 'slug' => 'romantic', 'type' => 'style'],
            ['name' => 'Beach', 'slug' => 'beach', 'type' => 'location'],
            ['name' => 'City Center', 'slug' => 'city-center', 'type' => 'location'],
        ];

        foreach ($tags as $tag) {
            DB::table('tags')->updateOrInsert(
                ['slug' => $tag['slug']],
                array_merge($tag, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedHouseRules(): void
    {
        $this->command->info('Skipping House Rules (room-specific)...');
        // House rules are room-specific in the migration
        // They will be added when creating/editing rooms
    }

    private function seedNotificationTemplates(): void
    {
        $this->command->info('Creating Notification Templates...');

        $templates = [
            [
                'name' => 'Booking Confirmed',
                'slug' => 'booking-confirmed',
                'type' => 'email',
                'event' => 'booking_confirmed',
                'subject' => 'Booking Confirmed - {{booking_code}}',
                'body' => 'Your booking {{booking_code}} has been confirmed.',
            ],
            [
                'name' => 'Payment Received',
                'slug' => 'payment-received',
                'type' => 'email',
                'event' => 'payment_received',
                'subject' => 'Payment Received',
                'body' => 'We have received your payment of {{amount}}',
            ],
            [
                'name' => 'Review Reminder',
                'slug' => 'review-reminder',
                'type' => 'email',
                'event' => 'review_reminder',
                'subject' => 'How was your stay?',
                'body' => 'Please leave a review for your recent stay.',
            ],
            [
                'name' => 'Booking Confirmed Push',
                'slug' => 'booking-confirmed-push',
                'type' => 'push',
                'event' => 'booking_confirmed',
                'subject' => null,
                'body' => 'Your booking has been confirmed!',
            ],
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                ['slug' => $template['slug']],
                array_merge($template, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
