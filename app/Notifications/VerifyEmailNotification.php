<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Get company settings
        $companySettings = [
            'name' => \App\Helpers\SettingsHelper::getCompanySettings()['name'] ?? 'Veesta',
            'address' => \App\Helpers\SettingsHelper::getCompanySettings()['address'] ?? '',
            'support_email' => \App\Helpers\SettingsHelper::getCompanySettings()['support_email'] ?? '',
            'support_phone' => \App\Helpers\SettingsHelper::getCompanySettings()['support_phone'] ?? '',
        ];

        return (new MailMessage)
            ->subject('Verify Your Email Address - Veesta')
            ->view('vendor.notifications.verify-email', [
                'actionUrl' => $verificationUrl,
                'notifiable' => $notifiable,
                'companySettings' => $companySettings,
            ]);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
