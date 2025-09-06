<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\AdminController;

class TestAdminN3tDataBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-n3tdata-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test AdminController getN3tdataBalance method';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing AdminController N3tdata Balance Method...');
        $this->newLine();

        try {
            // Create an instance of AdminController
            $adminController = new AdminController();

            // Call the getN3tdataBalance method
            $balance = $adminController->getN3tdataBalance();

            $this->info('✅ AdminController getN3tdataBalance() executed successfully');
            $this->info("💰 Current N3tdata Balance: ₦{$balance}");

            if ($balance > 0) {
                $this->info('🎉 Balance is available for data subscriptions');

                // Show some recommendations based on balance
                if ($balance > 1000) {
                    $this->info('💡 Balance is sufficient for multiple data subscriptions');
                } elseif ($balance > 100) {
                    $this->info('⚠️  Balance is moderate - monitor for low balance');
                } else {
                    $this->warn('🚨 Balance is low - consider topping up soon');
                }
            } else {
                $this->warn('⚠️  No balance available or connection failed');
                $this->info('💡 Check logs for detailed error information');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error testing AdminController balance method:');
            $this->error('   ' . $e->getMessage());

            $this->info('💡 This could be due to:');
            $this->info('   - Network connectivity issues');
            $this->info('   - Invalid N3tdata credentials');
            $this->info('   - N3tdata API temporarily unavailable');
        }

        $this->newLine();
        $this->info('Test completed!');

        return 0;
    }
}
