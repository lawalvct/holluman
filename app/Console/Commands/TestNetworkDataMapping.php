<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Network;
use App\Models\SubscriptionPlan;
use App\Helpers\N3tDataHelper;

class TestNetworkDataMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:network-data-mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the updated network-based data plan mapping functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Network-Based Data Plan Mapping...');

        // Get all networks and plans
        $networks = Network::all();
        $plans = SubscriptionPlan::active()->take(2)->get();

        if ($networks->isEmpty()) {
            $this->error('No networks found in database.');
            return;
        }

        if ($plans->isEmpty()) {
            $this->error('No active subscription plans found in database.');
            return;
        }

        $helper = new N3tDataHelper();

        $this->info("\nTesting Data Plan Mapping (Network-Based):");
        $this->info(str_repeat('=', 80));

        foreach ($networks as $network) {
            $this->info("\n🌐 Network: {$network->name} (ID: {$network->id})");
            $this->info("   Code: {$network->code}");
            $this->info("   N3tdata PlainID: " . ($network->n3tdata_plainid ?? 'Not Set'));

            $this->info("   📊 Testing plans with this network:");
            $this->info("   " . str_repeat('-', 60));

            foreach ($plans as $plan) {
                $mappedId = $helper->mapDataPlanId($plan, $network->id);

                $this->info("     Plan: {$plan->name} (ID: {$plan->id})");
                $this->info("     -> Mapped Data Plan ID: {$mappedId}");

                if (!$network->n3tdata_plainid) {
                    $this->warn("     ⚠️  Using fallback mapping (network n3tdata_plainid not set)");
                } else {
                    $this->info("     ✅ Using network's n3tdata_plainid: {$network->n3tdata_plainid}");
                }
                $this->info("");
            }
        }

        $this->info("\n" . str_repeat('=', 80));
        $this->info("📝 Summary:");
        $this->info("- Data plan mapping now uses network's n3tdata_plainid instead of plan's plainid");
        $this->info("- Set n3tdata_plainid for each network in admin interface for proper mapping");
        $this->info("- System falls back to default mapping when n3tdata_plainid is not set");

        // Count networks without n3tdata_plainid
        $networksWithoutPlainId = $networks->whereNull('n3tdata_plainid')->count();

        if ($networksWithoutPlainId > 0) {
            $this->warn("\n⚠️  {$networksWithoutPlainId} network(s) don't have n3tdata_plainid set");
            $this->info("   Consider setting these in the admin interface for accurate API integration");
        } else {
            $this->info("\n✅ All networks have n3tdata_plainid configured!");
        }
    }
}
