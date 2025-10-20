<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestVerificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:verification-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email verification notification to the specified user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        if ($user->hasVerifiedEmail()) {
            $this->warn("User {$user->name} ({$email}) has already verified their email!");
            $this->info("Sending verification email anyway for testing purposes...");
        } else {
            $this->info("Sending verification email to {$user->name} ({$email})...");
        }

        try {
            $user->sendEmailVerificationNotification();
            $this->info("✅ Verification email sent successfully!");
            $this->info("Check your Mailtrap inbox to see the email.");
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send verification email: " . $e->getMessage());
            return 1;
        }
    }
}
