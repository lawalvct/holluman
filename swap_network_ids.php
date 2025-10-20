<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    echo "Starting network ID swap...\n";

    // Get current records
    $glo = DB::table('networks')->where('id', 2)->first();
    $airtel = DB::table('networks')->where('id', 3)->first();

    echo "Current:\n";
    echo "  ID 2: {$glo->name}\n";
    echo "  ID 3: {$airtel->name}\n\n";

    // Swap IDs using a temporary ID
    DB::table('networks')->where('id', 2)->update(['id' => 999]);
    DB::table('networks')->where('id', 3)->update(['id' => 2]);
    DB::table('networks')->where('id', 999)->update(['id' => 3]);

    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    DB::commit();

    echo "After swap:\n";
    $airtelNew = DB::table('networks')->where('id', 2)->first();
    $gloNew = DB::table('networks')->where('id', 3)->first();
    echo "  ID 2: {$airtelNew->name}\n";
    echo "  ID 3: {$gloNew->name}\n\n";

    echo "✅ Successfully swapped! AIRTEL is now ID 2 and GLO is now ID 3\n";

} catch (Exception $e) {
    DB::rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
