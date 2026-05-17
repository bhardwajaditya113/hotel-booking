<?php
// scripts/run_test_payment.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Find demo guest user
$user = User::where('email', 'demo.guest@elapse.test')->first();
if (! $user) {
    echo "Demo guest user not found\n";
    exit(1);
}

// Find an unpaid booking for this user
$booking = Booking::where('user_id', $user->id)->where('payment_status', 0)->first();
if (! $booking) {
    echo "No unpaid booking found for demo guest (user id: {$user->id})\n";
    exit(1);
}

// Log in as the demo user
Auth::loginUsingId($user->id);

// Prepare request
$request = Request::create('/razorpay/test-payment', 'POST', ['booking_id' => $booking->id]);
$request->setMethod('POST');

// Call controller
$controller = new App\Http\Controllers\Frontend\RazorpayController();
try {
    $response = $controller->testPayment($request);
    echo "Test payment executed. Booking ID: {$booking->id}\n";
    echo "Booking payment_status after: " . Booking::find($booking->id)->payment_status . "\n";
} catch (Throwable $e) {
    echo "Test payment failed: " . $e->getMessage() . "\n";
    exit(1);
}
