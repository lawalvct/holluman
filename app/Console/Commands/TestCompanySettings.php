<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Helpers\SettingsHelper;

class TestCompanySettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:company-settings {--set : Set sample company settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test company settings functionality and optionally set sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('set')) {
            $this->setSampleSettings();
        }

        $this->testSettings();
        return 0;
    }

    /**
     * Set sample company settings
     */
    private function setSampleSettings()
    {
        $this->info('Setting sample company settings...');

        $settings = [
            'company_name' => 'Holluman Networks',
            'company_logo' => 'images/holluman-logo.png',
            'company_address' => '123 Innovation Drive, Victoria Island, Lagos, Nigeria',
            'company_email' => 'info@holluman.com',
            'support_phone' => '+234 800 123 4567',
            'support_email' => 'support@holluman.com',
            'app_description' => 'Premium internet data subscription service for Nigeria. Fast, reliable, and affordable data plans.',
            'app_keywords' => 'internet, data, subscription, nigeria, mtn, glo, airtel, 9mobile',
            'app_author' => 'Holluman Networks'
        ];

        foreach ($settings as $slug => $value) {
            Setting::setValue($slug, $value);
            $this->info("✅ Set {$slug}: {$value}");
        }

        $this->info('✅ Sample settings have been set!');
        $this->newLine();
    }

    /**
     * Test settings functionality
     */
    private function testSettings()
    {
        $this->info('Testing SettingsHelper functionality...');
        $this->newLine();

        // Test individual methods
        $this->info('📊 Individual Setting Methods:');
        $this->info('Company Name: ' . SettingsHelper::getCompanyName());
        $this->info('Company Logo: ' . SettingsHelper::getCompanyLogo());
        $this->info('Company Address: ' . SettingsHelper::getCompanyAddress());
        $this->info('Company Email: ' . SettingsHelper::getCompanyEmail());
        $this->info('Support Phone: ' . SettingsHelper::getSupportPhone());
        $this->info('Support Email: ' . SettingsHelper::getSupportEmail());

        $this->newLine();

        // Test bulk methods
        $this->info('📦 Bulk Setting Methods:');
        $companySettings = SettingsHelper::getCompanySettings();
        $this->table(
            ['Setting', 'Value'],
            [
                ['Company Name', $companySettings['name']],
                ['Company Logo', $companySettings['logo']],
                ['Company Address', $companySettings['address']],
                ['Company Email', $companySettings['email']],
                ['Support Phone', $companySettings['support_phone']],
                ['Support Email', $companySettings['support_email']],
            ]
        );

        $this->newLine();

        // Test meta settings
        $this->info('🔍 Meta Settings:');
        $metaSettings = SettingsHelper::getMetaSettings();
        $this->table(
            ['Meta Setting', 'Value'],
            [
                ['Description', $metaSettings['description']],
                ['Keywords', $metaSettings['keywords']],
                ['Author', $metaSettings['author']],
            ]
        );

        $this->newLine();

        // Test direct Setting model usage
        $this->info('🗃️ Direct Setting Model Test:');
        $directTest = Setting::getValue('company_name', 'Default Company');
        $this->info('Direct model call result: ' . $directTest);

        $this->newLine();
        $this->info('✅ All tests completed successfully!');
        $this->newLine();
        $this->info('💡 Usage Tips:');
        $this->info('   - Run with --set to create sample settings');
        $this->info('   - Update settings in admin panel at /admin/settings');
        $this->info('   - Settings are cached for performance');
        $this->info('   - Company logo should be in public/images/ directory');
    }
}
