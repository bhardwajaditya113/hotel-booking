<?php
// Ensure there is at least one pending incoming booking for demo.host@elapse.test
require __DIR__ . "/../vendor/autoload.php";

use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;

$base = dirname(__DIR__);
chdir($base);

$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hostEmail = getenv('HOST_EMAIL') ?: 'demo.host@elapse.test';
$guestEmail = getenv('GUEST_EMAIL') ?: 'demo.guest@elapse.test';

$host = User::where('email', $hostEmail)->first();
$guest = User::where('email', $guestEmail)->first();

if (! $host) { echo "Host {$hostEmail} not found\n"; exit(1); }
if (! $guest) { echo "Guest {$guestEmail} not found\n"; exit(1); }

$property = Property::where('user_id', $host->id)->first();
if (! $property) {
    // pick any property and assign to host
    $property = Property::first();
    if (! $property) { echo "No property found to assign to host\n"; exit(2); }
    $property->update(['user_id' => $host->id]);
    echo "Assigned property {$property->id} to host {$hostEmail}\n";
}

$room = Room::where('property_id', $property->id)->first();
if (! $room) {
    // create a minimal room record if absent
    $room = Room::create([
        'property_id' => $property->id,
        'name' => 'Demo Room',
        'price' => 1000,
        'status' => 'Active',
    ]);
    echo "Created demo room {$room->id} for property {$property->id}\n";
}

// Find an existing booking for this property that is awaiting host approval
$booking = Booking::where('property_id', $property->id)
    ->where('host_approval_status', 'pending')
    ->first();

if ($booking) {
    echo "Found existing pending booking {$booking->id}\n";
    exit(0);
}

// Otherwise create a new booking for the guest
$checkIn = now()->addDays(15)->toDateString();
$checkOut = now()->addDays(17)->toDateString();

$booking = Booking::create([
    'user_id' => $guest->id,
    'property_id' => $property->id,
    'rooms_id' => $room->id,
    'name' => $guest->name,
    'email' => $guest->email,
    'phone' => $guest->phone ?? '',
    'check_in' => $checkIn,
    'check_out' => $checkOut,
    'nights' => 2,
    'total_price' => 2000,
    'status' => 0, // pending
    'host_approval_status' => 'pending',
]);

echo "Created booking {$booking->id} for guest {$guestEmail} on property {$property->id} (pending)\n";
exit(0);
