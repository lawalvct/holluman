<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Nomba Payment Settings
            [
                'name' => 'Nomba Account ID',
                'slug' => 'nombaAccountID',
                'value' => env('NOMBA_ACCOUNT_ID', ''),
            ],
            [
                'name' => 'Nomba Client ID',
                'slug' => 'nombaClientID',
                'value' => env('NOMBA_CLIENT_ID', ''),
            ],
            [
                'name' => 'Nomba Private Key',
                'slug' => 'nombaPrivatekey',
                'value' => env('NOMBA_PRIVATE_KEY', ''),
            ],
            [
                'name' => 'Nomba Webhook Secret',
                'slug' => 'nombaWebhookSecret',
                'value' => env('NOMBA_WEBHOOK_SECRET', ''),
            ],

            // Application Settings
            [
                'name' => 'Application Name',
                'slug' => 'app_name',
                'value' => env('APP_NAME', 'Holluman ISP'),
            ],
            [
                'name' => 'Application URL',
                'slug' => 'app_url',
                'value' => env('APP_URL', 'http://localhost'),
            ],
            [
                'name' => 'Support Email',
                'slug' => 'support_email',
                'value' => env('MAIL_FROM_ADDRESS', 'support@example.com'),
            ],
            [
                'name' => 'Support Phone',
                'slug' => 'support_phone',
                'value' => '+234 800 000 0000',
            ],

            // Business Settings
            [
                'name' => 'Default Currency',
                'slug' => 'default_currency',
                'value' => 'NGN',
            ],
            [
                'name' => 'USD to NGN Exchange Rate',
                'slug' => 'usd_to_ngn_rate',
                'value' => '1500',
            ],
            [
                'name' => 'Tax Rate (%)',
                'slug' => 'tax_rate',
                'value' => '7.5',
            ],

            // Email Settings
            [
                'name' => 'Email Notifications Enabled',
                'slug' => 'email_notifications',
                'value' => 'true',
            ],
            [
                'name' => 'SMS Notifications Enabled',
                'slug' => 'sms_notifications',
                'value' => 'false',
            ],

            // Subscription Settings
            [
                'name' => 'Trial Period (Days)',
                'slug' => 'trial_period_days',
                'value' => '7',
            ],
            [
                'name' => 'Auto Renewal Enabled',
                'slug' => 'auto_renewal',
                'value' => 'true',
            ],
            [
                'name' => 'Grace Period (Days)',
                'slug' => 'grace_period_days',
                'value' => '3',
            ],

            // Network Settings
            [
                'name' => 'Default Network Provider',
                'slug' => 'default_network',
                'value' => 'MTN',
            ],
            [
                'name' => 'Network API Timeout (seconds)',
                'slug' => 'network_api_timeout',
                'value' => '30',
            ],

            // Payment Settings
            [
                'name' => 'Minimum Payment Amount',
                'slug' => 'min_payment_amount',
                'value' => '500',
            ],
            [
                'name' => 'Maximum Payment Amount',
                'slug' => 'max_payment_amount',
                'value' => '1000000',
            ],
            [
                'name' => 'Payment Gateway Timeout (seconds)',
                'slug' => 'payment_timeout',
                'value' => '300',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['slug' => $setting['slug']],
                [
                    'name' => $setting['name'],
                    'value' => $setting['value'],
                ]
            );
        }
    }
}
