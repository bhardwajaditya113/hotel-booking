<?php

namespace Tests\Unit\Marketplace;

use App\Models\Booking;
use App\Services\Marketplace\BookingMarketplaceSplitCalculator;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BookingMarketplaceSplitCalculatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('marketplace.platform_fee_percent', 10);
        Config::set('marketplace.platform_fee_fixed_inr', 0);
        Config::set('marketplace.gst_percent_on_platform_fee', 18);
    }

    public function test_guest_total_inr_scales_small_amounts_like_usd_placeholder(): void
    {
        $booking = new Booking(['total_price' => 50]);

        $this->assertSame(4150.0, BookingMarketplaceSplitCalculator::guestTotalInrFromBooking($booking));
    }

    public function test_compute_splits_commission_and_gst_then_host_remainder(): void
    {
        $split = BookingMarketplaceSplitCalculator::compute(1000);

        $this->assertSame(1000.0, $split['guest_total_inr']);
        $this->assertSame(100.0, $split['platform_fee_base_inr']);
        $this->assertSame(18.0, $split['platform_gst_inr']);
        $this->assertSame(882.0, $split['host_payout_inr']);
        $this->assertSame(100_000, $split['total_paise']);
        $this->assertSame(88_200, $split['host_transfer_paise']);
    }
}
