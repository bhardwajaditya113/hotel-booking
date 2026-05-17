<?php
// Script: credit demo guest wallet (safe test helper)
require __DIR__ . "/../vendor/autoload.php";

use App\Models\User;
use App\Models\Wallet;

$base = dirname(__DIR__);
chdir($base);

// Bootstrap Laravel
$app = require $base . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find demo guest
$email = getenv('GUEST_EMAIL') ?: 'demo.guest@elapse.test';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "User {$email} not found\n";
    exit(1);
}

$wallet = Wallet::getOrCreate($user->id);
$amount = 500.00;
$tx = $wallet->credit($amount, 'deposit', 'Automated test top-up for UI scenario', null, null, ['source' => 'scripts/topup_wallet.php']);

if ($tx) {
    echo "Credited ₹{$amount} to {$email} wallet. New balance: {$wallet->formatted_balance}\n";
    exit(0);
}

echo "Failed to credit wallet.\n";
exit(2);
