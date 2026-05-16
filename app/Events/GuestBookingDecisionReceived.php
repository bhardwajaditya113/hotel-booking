<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GuestBookingDecisionReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  'approved'|'declined'  $decision
     */
    public function __construct(
        public int $guestUserId,
        public int $bookingId,
        public string $bookingCode,
        public string $decision,
        public ?string $payUrl = null,
        public ?string $declineReason = null,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.'.$this->guestUserId)];
    }

    public function broadcastAs(): string
    {
        return 'GuestBookingDecisionReceived';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'booking_id' => $this->bookingId,
            'booking_code' => $this->bookingCode,
            'decision' => $this->decision,
            'pay_url' => $this->payUrl,
            'decline_reason' => $this->declineReason,
        ];
    }
}
