<?php
// scripts/update_razorpay.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PaymentMethod;

$pm = PaymentMethod::where('provider', 'razorpay')->first();
if ($pm) {
    $pm->is_active = 1;
    $pm->is_default = 1;
    $pm->supported_currencies = json_encode(['INR']);
    $pm->transaction_fee = 2.5;
    $pm->config = ['key_id' => env('RAZORPAY_KEY_ID'), 'key_secret' => env('RAZORPAY_KEY_SECRET')];
    $pm->save();
    echo "PaymentMethod updated\n";
} else {
    echo "PaymentMethod (razorpay) not found\n";
}
