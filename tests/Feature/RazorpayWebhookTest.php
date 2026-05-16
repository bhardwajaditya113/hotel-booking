<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RazorpayWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.razorpay.webhook_secret' => 'test_whsec_marketplace']);
    }

    public function test_rejects_invalid_signature(): void
    {
        $body = '{"event":"payment.captured","payload":{"payment":{"entity":{"id":"pay_test","order_id":"order_test"}}}}';

        $this->call('POST', '/webhooks/razorpay', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => 'invalid',
        ], $body)->assertStatus(400);
    }

    public function test_payment_captured_completes_booking_when_booking_matches_order(): void
    {
        $user = User::factory()->create();

        $booking = Booking::create([
            'user_id' => $user->id,
            'total_price' => 500,
            'payment_status' => 0,
            'status' => 0,
            'payment_method' => 'Razorpay',
            'razorpay_order_id' => 'order_wh_match',
            'name' => 'Webhook Guest',
            'email' => 'webhook@guest.test',
        ]);

        $payload = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_wh_ok',
                        'order_id' => 'order_wh_match',
                        'notes' => [
                            'booking_id' => (string) $booking->id,
                        ],
                    ],
                ],
            ],
        ];

        $body = json_encode($payload);
        $sig = hash_hmac('sha256', $body, 'test_whsec_marketplace');

        $this->call('POST', '/webhooks/razorpay', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => $sig,
        ], $body)->assertOk();

        $booking->refresh();
        $this->assertSame(1, (int) $booking->payment_status);
        $this->assertSame('pay_wh_ok', $booking->transation_id);
    }
}
