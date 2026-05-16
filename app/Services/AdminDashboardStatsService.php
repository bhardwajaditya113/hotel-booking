<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\HostProfile;
use App\Models\Property;
use App\Models\Review;
use Carbon\Carbon;

class AdminDashboardStatsService
{
    /**
     * Live dashboard metrics from the same database the public site uses.
     */
    public static function snapshot(): array
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        $startThisWeek = $now->copy()->startOfWeek();
        $endThisWeek = $now->copy();
        $startLastWeek = $now->copy()->subWeek()->startOfWeek();
        $endLastWeek = $now->copy()->subWeek()->endOfWeek();

        $bookingQuery = Booking::query();

        $totalBookings = (clone $bookingQuery)->count();
        $pendingBookings = (clone $bookingQuery)->where('status', 0)->count();
        $completeBookings = (clone $bookingQuery)->where('status', 1)->count();

        $totalRevenue = (float) (clone $bookingQuery)->sum('total_price');
        $todayRevenue = (float) (clone $bookingQuery)->whereDate('created_at', $today)->sum('total_price');

        $revThisWeek = (float) Booking::query()
            ->whereBetween('created_at', [$startThisWeek, $endThisWeek])
            ->sum('total_price');
        $revLastWeek = (float) Booking::query()
            ->whereBetween('created_at', [$startLastWeek, $endLastWeek])
            ->sum('total_price');

        $pendingThisWeek = Booking::query()
            ->where('status', 0)
            ->whereBetween('created_at', [$startThisWeek, $endThisWeek])
            ->count();
        $pendingLastWeek = Booking::query()
            ->where('status', 0)
            ->whereBetween('created_at', [$startLastWeek, $endLastWeek])
            ->count();

        $completeThisWeek = Booking::query()
            ->where('status', 1)
            ->whereBetween('created_at', [$startThisWeek, $endThisWeek])
            ->count();
        $completeLastWeek = Booking::query()
            ->where('status', 1)
            ->whereBetween('created_at', [$startLastWeek, $endLastWeek])
            ->count();

        $chartLabels = [];
        $chartAmounts = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $chartLabels[] = $day->format('M j');
            $chartAmounts[] = (float) Booking::query()
                ->whereDate('created_at', $day->toDateString())
                ->sum('total_price');
        }

        return [
            'generated_at' => $now->toIso8601String(),
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'complete_bookings' => $completeBookings,
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'pending_week_pct' => self::pctChange($pendingLastWeek, $pendingThisWeek),
            'complete_week_pct' => self::pctChange($completeLastWeek, $completeThisWeek),
            'revenue_week_pct' => self::pctChange($revLastWeek, $revThisWeek),
            'chart_labels' => $chartLabels,
            'chart_amounts' => $chartAmounts,
            'total_properties' => Property::query()->count(),
            'pending_properties' => Property::query()->where('verification_status', 'pending')->count(),
            'verified_properties' => Property::query()->where('verification_status', 'verified')->count(),
            'hotel_properties' => Property::query()->where('listing_type', 'hotel')->count(),
            'unique_stay_properties' => Property::query()->where('listing_type', 'unique_stay')->count(),
            'total_hosts' => HostProfile::query()->count(),
            'pending_hosts' => HostProfile::query()->where('verification_status', 'pending')->count(),
            'verified_hosts' => HostProfile::query()->where('verification_status', 'verified')->count(),
            'superhosts' => HostProfile::query()->where('is_superhost', true)->count(),
            'total_reviews' => Review::query()->count(),
            'pending_reviews' => Review::query()->where('is_approved', false)->count(),
            'approved_reviews' => Review::query()->where('is_approved', true)->count(),
        ];
    }

    private static function pctChange(int|float $previous, int|float $current): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
