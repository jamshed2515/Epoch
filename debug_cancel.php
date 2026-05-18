<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Appointment;
use App\Models\User;

// Check appointment 33 and a few others
$ids = [33, 31, 32];
foreach ($ids as $id) {
    $a = Appointment::find($id);
    if (!$a) continue;
    echo "Appointment #{$a->id}:\n";
    echo "  status        = {$a->status}\n";
    echo "  date          = {$a->appointment_date}\n";
    echo "  isFuture      = " . ($a->appointment_date->isFuture() ? 'YES' : 'NO') . "\n";
    echo "  canCancel     = " . ($a->canBeCancelledByUser() ? 'YES' : 'NO') . "\n";
    echo "  user_id       = {$a->user_id}\n";
    $user = User::find($a->user_id);
    echo "  user name     = " . ($user ? $user->name : 'N/A') . "\n";
    echo "\n";
}

echo "Today = " . today() . "\n";
echo "Now   = " . now() . "\n";
