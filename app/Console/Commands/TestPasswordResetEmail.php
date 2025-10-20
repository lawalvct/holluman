<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class TestPasswordResetEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test password reset email to the specified user';

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

        $this->info("Sending password reset email to {$user->name} ({$email})...");

        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->info("✅ Password reset email sent successfully!");
            $this->info("Check your Mailtrap inbox to see the email.");
            return 0;
        } else {
            $this->error("❌ Failed to send password reset email: " . $status);
            return 1;
        }
    }
}
