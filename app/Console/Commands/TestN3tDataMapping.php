<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Network;
use App\Helpers\N3tDataHelper;

class TestN3tDataMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:n3tdata-mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test N3tdata network mapping functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing N3tdata Network Mapping...');

        // Get all networks
        $networks = Network::all();

        if ($networks->isEmpty()) {
            $this->error('No networks found in database.');
            return;
        }

        $helper = new N3tDataHelper();

        $this->info("\nNetwork Mapping Results:");
        $this->info(str_repeat('-', 80));

        foreach ($networks as $network) {
            $mappedId = $helper->mapNetworkId($network->id);

            $this->info("Network ID: {$network->id}");
            $this->info("Network Name: {$network->name}");
            $this->info("Network Code: {$network->code}");
            $this->info("N3tdata PlainID: " . ($network->n3tdata_plainid ?? 'Not Set'));
            $this->info("Mapped ID: {$mappedId}");

            if (!$network->n3tdata_plainid) {
                $this->warn("⚠️  N3tdata PlainID not set for {$network->name} - using fallback mapping");
            } else {
                $this->info("✅ Using N3tdata PlainID for {$network->name}");
            }

            $this->info(str_repeat('-', 40));
        }

        $this->info("\nTest completed!");
        $this->info("To set N3tdata PlainIDs, use the admin interface or update networks directly.");
    }
}
