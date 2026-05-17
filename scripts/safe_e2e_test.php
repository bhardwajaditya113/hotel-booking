<?php
// scripts/safe_e2e_test.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "Starting safe end-to-end payment test (no real charge)\n";

$user = User::where('email', 'demo.guest@elapse.test')->first();
if (! $user) {
    echo "Demo user not found. Abort.\n";
    exit(1);
}

// Find unpaid booking for demo user
$booking = Booking::where('user_id', $user->id)->where('payment_status', 0)->first();

if (! $booking) {
    echo "No unpaid booking found for demo user. Attempting to clone an existing booking for demo user...\n";
    // Try to find any booking and clone for demo user
    $src = Booking::first();
    if (! $src) {
        echo "No bookings exist to clone. Abort.\n";
        exit(1);
    }
    $clone = $src->replicate();
    $clone->user_id = $user->id;
    $clone->payment_status = 0;
    $clone->razorpay_order_id = null;
    $clone->code = 'SAFE-E2E-'.time();
    $clone->save();
    $booking = $clone;
    echo "Cloned booking id {$booking->id} for demo user.\n";
} else {
    echo "Found unpaid booking id {$booking->id} for demo user.\n";
}

// Login as demo user
Auth::loginUsingId($user->id);

// Prepare request and call testPayment
$request = Request::create('/razorpay/test-payment', 'POST', ['booking_id' => $booking->id]);
$request->setMethod('POST');

$controller = new App\Http\Controllers\Frontend\RazorpayController();
try {
    $resp = $controller->testPayment($request);
    echo "testPayment controller returned.\n";
} catch (Throwable $e) {
    echo "Test payment call failed: " . $e->getMessage() . "\n";
    exit(1);
}

$booking->refresh();
$paid = $booking->payment_status;
echo "Booking {$booking->id} payment_status: {$paid}\n";
if ($paid == 1) {
    echo "SAFE E2E: SUCCESS — booking completed without contacting Razorpay.\n";
} else {
    echo "SAFE E2E: FAILURE — booking not marked paid.\n";
}

// Show recent payment transactions for booking (if any)
$txns = $booking->paymentTransactions()->orderBy('created_at', 'desc')->limit(5)->get();
if ($txns->isEmpty()) {
    echo "No payment transactions found for booking.\n";
} else {
    echo "Recent transactions:\n";
    foreach ($txns as $t) {
        echo " - id={$t->id} provider_transaction_id={$t->provider_transaction_id} amount={$t->amount} status={$t->status}\n";
    }
}

echo "Done.\n";
